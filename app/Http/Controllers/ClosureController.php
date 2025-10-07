<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FinancialAdvance;
use App\Models\ExpenseInvoice;
use App\Models\AdvanceInvoiceClosure;
use App\Services\ClosureService; //service class
use App\Notifications\ClosureRejected;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;
use App\Http\Helper\Helper;
use Carbon\Carbon;

class ClosureController extends Controller
{
    protected $closureService;

    public function __construct(ClosureService $closureService)
    {
        $this->closureService = $closureService;
        // التأكد من أن المستخدم مصرح له (عادةً الموظف)
        $this->middleware('auth'); 
    }

    // عرض شاشة التقفيل للموظف
    public function showClosureForm()
    {
        if (!Helper::checkPermission(23)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        // إذا كان الدور ليس ضمن الأدوار ذات الصلاحية الكاملة (مثل الكاشير):
        if (!in_array(auth()->user()->role, ['cashier'])) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }


        $userId = auth()->id();
        
        // جلب العهد المتاحة للتقفيل (Confirmed أو Partially Closed)
        $availableAdvances = FinancialAdvance::where('user_id', $userId)
            ->whereIn('status', ['Confirmed', 'Partially Closed'])
            ->where('remaining_balance', '>', 0)
            ->get();

        // جلب الفواتير المتاحة للتقفيل (Approved ولم تستخدم بالكامل)
        $availableInvoices = ExpenseInvoice::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereRaw('amount > used_amount')
            ->get();
        
        // ********** إضافة الحسابات الإجمالية **********
        // 1. إجمالي أرصدة العهد المتاحة
        $totalAvailableAdvanceAmount = $availableAdvances->sum('remaining_balance');
        
        // 2. إجمالي مبالغ الفواتير المتاحة
        $totalAvailableInvoiceAmount = $availableInvoices->sum(function ($invoice) {
            return $invoice->amount - $invoice->used_amount;
        });
    

        return view('closures.form', compact(
            'availableAdvances', 
            'availableInvoices',
            'totalAvailableAdvanceAmount', 
            'totalAvailableInvoiceAmount'  
        ));
    }

    // معالجة طلب التقفيل
    
    public function processClosure(Request $request)
    {

        if (!Helper::checkPermission(23) || !in_array(auth()->user()->role, ['cashier'])) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        // 1. التحقق من صحة البيانات المدخلة (تم التعديل هنا)
        $request->validate([
            'advance_id' => 'required|exists:financial_advances,id',
            // يجب أن تكون مصفوفة ولا تقل عن عنصر واحد
            'invoices' => 'required|array|min:1', 
            // التحقق من أن كل عنصر في المصفوفة يحتوي على معرف فاتورة ومبلغ صحيح
            'invoices.*.invoice_id' => 'required|exists:expense_invoices,id',
            'invoices.*.closed_amount' => 'required|numeric|min:0.01',
        ]);
        
        $invoicesData = $request->input('invoices');

        try {
            DB::beginTransaction();
            $advance = FinancialAdvance::findOrFail($request->input('advance_id'));
            
            // تأكيد ملكية الموظف للعهدة
            if ($advance->user_id != auth()->id()) {
                 return back()->with('error', 'لا تملك صلاحية لربط هذه العهدة.');
            }

            // 2. تنفيذ المنطق الأساسي عبر الخدمة (باستخدام الدالة الجديدة)
            $closures = $this->closureService->performMultiInvoiceClosure($advance, $invoicesData, auth()->id());
            
            $count = count($closures);
            // حساب المبلغ الإجمالي المقفل
            $totalAmount = array_sum(array_column($closures, 'closed_amount'));

   
            DB::commit();

        
            return redirect()->route('closures.form')->with('success', 
                "تم تقفيل المبلغ بنجاح! عدد الفواتير المستخدمة: **$count**، إجمالي المبلغ المقفل: " . number_format($totalAmount, 2)
            );

        } catch (\Exception $e) {
            DB::rollback();
            // رسائل الخطأ من الخدمة ستظهر هنا (مثل تجاوز الرصيد المتبقي للعهدة)
            return back()->with('error', 'فشل عملية التقفيل: ' . $e->getMessage());
        }
    }





    // التقفيل النهائي (المحاسب)
    public function approveClosure(AdvanceInvoiceClosure $closure)
    {
        // تحقق من صلاحية المحاسب هنا (باستخدام Middleware أو Gate)
        if (auth()->user()->role !== 'accountant') {
            return back()->with('error', 'فقط المحاسبون مخولون باعتماد التقفيل.');
        }
        
        // منع التعديل إذا كان معتمدًا مسبقًا
        if ($closure->accountant_approved) {
            return back()->with('error', 'هذا التقفيل معتمد مسبقاً.');
        }

        // تحديث سجل التقفيل
        $closure->update([
            'accountant_approved' => true,
            'accountant_approval_date' => Carbon::now(),
            // يمكن إضافة closed_by_accountant_id للتسجيل
        ]);
        
        // المنع التلقائي للتعديل أو الإلغاء
        // بما أننا نعتمد على حقل 'accountant_approved' كحاجز، يجب على أي عملية إلغاء أو تعديل (في المستقبل) أن تتحقق منه أولاً.

        return back()->with('success', 'تم اعتماد عملية التقفيل بنجاح.');
    }

    // عرض قائمة التقفيلات التي تحتاج لاعتماد نهائي من المحاسب
    public function reviewClosures()
    {
        if (!Helper::checkPermission(24)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        // إذا كان الدور ليس ضمن الأدوار ذات الصلاحية الكاملة (مثل الكاشير):
        if (!in_array(auth()->user()->role, ['accountant','admin'])) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        // جلب جميع عمليات التقفيل التي لم يتم اعتمادها بعد
        $pendingClosures = AdvanceInvoiceClosure::with(['advance.recipient', 'invoice'])
            ->where('accountant_approved', false)
            ->where('is_rejected',false)
            ->orderBy('closure_date', 'asc')
            ->get();

        return view('closures.closure_review', compact('pendingClosures'));
    }



    // معالجة طلب التقفيل التلقائي
    public function processAutoClosure(Request $request)
    {
        $userId = auth()->id();
        
        
        try {
            $closures = $this->closureService->performAutoClosure($userId);
            
            $count = count($closures);
            if ($count > 0) {
                $totalAmount = array_sum(array_map(fn($c) => $c->closed_amount, $closures));
                return redirect()->route('closures.form')->with('success', 
                    "تم تنفيذ $count عملية تقفيل تلقائية بنجاح، بإجمالي مبلغ مقفل قدره: " . number_format($totalAmount, 2)
                );
            } else {
                return redirect()->route('closures.form')->with('info', 'لا توجد عهد أو فواتير متاحة للتقفيل التلقائي.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'فشل عملية التقفيل التلقائي: ' . $e->getMessage());
        }
    }


    /**
     * رفض عملية التقفيل وإلغاء آثارها المالية.
     * * يتطلب هذا الإجراء إدخال سبب الرفض وإلغاء التحديثات المالية التي تمت على العهدة والفاتورة.
     * يجب أن يكون المحاسب (accountant) هو من يقوم بالرفض.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AdvanceInvoiceClosure $closure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectClosure(Request $request, AdvanceInvoiceClosure $closure)
    {
        // تحقق من صلاحية المحاسب
        if (auth()->user()->role !== 'accountant' && auth()->user()->role !== 'admin') {
            return back()->with('error', 'فقط المحاسبون مخولون برفض التقفيل.');
        }

        // 1. تحقق من الحالة
        if ($closure->accountant_approved) {
            return back()->with('error', 'لا يمكن رفض تقفيل تم اعتماده مسبقاً.');
        }
        if ($closure->is_rejected) {
            return back()->with('error', 'هذا التقفيل مرفوض مسبقاً.');
        }

        // 2. التحقق من سبب الرفض (نفترض هنا أن السبب يتم إرساله من مودال في التطبيق الفعلي)
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ], [
            'rejection_reason.required' => 'يجب إدخال سبب واضح لرفض عملية التقفيل.',
            'rejection_reason.min' => 'يجب أن لا يقل سبب الرفض عن 10 أحرف.',
        ]);

        $closedAmount = $closure->closed_amount;
        $advance = $closure->advance;
        $invoice = $closure->invoice;
        $rejectionReason = $request->input('rejection_reason');

        try {
            DB::beginTransaction();

            // 1. عكس التأثير المالي على الفاتورة
            $invoice->decrement('used_amount', $closedAmount);


            // 2. عكس التأثير المالي على العهدة (زيادة الرصيد المتبقي)
            $advance->increment('remaining_balance', $closedAmount);
            $advance->decrement('closed_amount', $closedAmount);


            // 3. تحديث حالة العهدة
            // إذا كانت العهدة مغلقة كلياً، نغير حالتها إلى Partially Closed
            if ($advance->status === 'Closed') {
                $advance->status = 'Partially Closed';
                $advance->save();
            }

            // 4. تحديث سجل التقفيل نفسه كـ مرفوض
            $closure->update([
                'is_rejected' => true,
                'rejection_reason' => $rejectionReason,
                'rejected_by_id' => auth()->id(),
                'rejected_at' => Carbon::now(),
            ]);

            // 4. Soft delete لسجل الإغلاق
            $closure->delete();

            DB::commit();

            // 5. إرسال إشعار للموظف صاحب الفاتورة/العهدة بالرفض (المنطق الجديد)
            $employee = $advance->recipient; 

            if ($employee) {
                // **منطق إرسال الإشعار**:
                // يجب استبدال هذا الكود بمنطق الإشعار الفعلي باستخدام نظام إشعارات Laravel (Notifications)
                
                $notificationMessage = "تم رفض عملية تقفيل الفاتورة رقم **{$invoice->invoice_no}** المرتبطة بالعهدة رقم **{$advance->id}** بواسطة المحاسب. 
                                      \nالسبب: **{$rejectionReason}**";

                $employee->notify(new ClosureRejected($invoice->id, $advance->id, $rejectionReason));
                
               
                // يمكنك إضافة رسالة تنبيه مؤقتة للمحاسب لتأكيد محاولة إرسال الإشعار
                session()->flash('info', "تم تجهيز إشعار الرفض للموظف **{$employee->name}** وسيتم إرساله عبر نظام الإشعارات.");
            }
            
            return back()->with('success', 'تم رفض عملية التقفيل بنجاح وتم إلغاء آثارها المالية. السبب: ' . $rejectionReason);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'فشل في رفض عملية التقفيل وعكس آثارها: ' . $e->getMessage());
        }
    }

}
