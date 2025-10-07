<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAdvance;
use App\Models\AdvanceInvoiceClosure;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Helper\Helper;
use Carbon\Carbon;
use App\Notifications\NewAdvancePendingConfirmation;

class FinancialAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // التحقق من صلاحية الوصول إلى النموذج (ID 19)
        if (!Helper::checkPermission(19)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }


        if ($request->ajax()) {

            // 1. استخدام auth()->id() أكثر نظافة وأماناً
            $userId = auth()->id();
            
            $query = FinancialAdvance::with('recipient')
                
                ->latest();

            // 2. تبسيط التحقق من الأدوار باستخدام مصفوفة
            $fullAccessRoles = ['admin', 'accountant'];
            $userRole = auth()->user()->role;
            
            // إذا كان الدور ليس ضمن الأدوار ذات الصلاحية الكاملة (مثل الكاشير):
            if (!in_array($userRole, $fullAccessRoles)) {
                // تطبيق قيد جلب العُهد الخاصة بالمستخدم الحالي فقط
                $query->where('user_id', $userId);
                      //->where('status', '!=', 'Closed');
            }
            // إذا كان الدور (admin أو accountant) سيتم جلب جميع البيانات المفتوحة

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if($request->filled('advanciesNeedForAproved') && $request->advanciesNeedForAproved == '1' ){
                $query->where('status', 'Pending');
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                // كلا التاريخين موجودين → فلترة بينهما
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->whereBetween('issue_date', [$dateFrom, $dateTo]);
            
            } elseif ($request->filled('date_from')) {
                // فقط من تاريخ موجود
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                $query->where('issue_date', '>=', $dateFrom);
            
            } elseif ($request->filled('date_to')) {
                // فقط إلى تاريخ موجود
                $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->where('issue_date', '<=', $dateTo);
            }
            
            $data = $query;
            
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('advance_status', function ($data) {
                    // 3. تنظيم عرض حالة العهدة باستخدام خريطة (Map) للحالات والألوان
                    $classMap = [
                        'Pending'          => 'light-danger', // قيد الانتظار
                        'Confirmed'        => 'light-warning', // تم التأكيد
                        'Partially Closed' => 'light-primary', // مسدد جزئياً
                        'Closed'           => 'light-success',  // مغلق (مسدد بالكامل)
                    ];

                    $class = $classMap[$data->status] ?? 'light-secondary'; // لون افتراضي للحالات غير المعرفة
                    
                    return '<span class="label label-inline label-' . $class . ' font-weight-bold">' . $data->status . '</span>';
                })
                
                ->addColumn('action', function ($data) {
                    $editBtn = '';
                    $AcceptBtn = ''; // تم تصحيح التسمية من deleverBtn إلى deliverBtn
                    $returnBtn = '';
                    $slipBtn = '';
                    $viewBtn = '';

                    // تعريف الأدوار المسموح لها بالعرض (Accountant, Cashier)
                    $allowViewRoles = ['accountant', 'cashier'];

                    if (in_array(auth()->user()->role, $allowViewRoles)) {
                        $viewBtn = '
                        <a href="' . route("advances.show", $data->id) . '" 
                            class="btn btn-icon btn-light-info me-2 mb-2 py-3"
                            title="عرض تفاصيل العهدة والفواتير المغلقة"
                            target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>';
                    }
                    
                    // === 1. زر تعديل حالة العهدة (ملاحظة: تم تعديل عنوان الزر ليتناسب مع 'العهدة' بدلاً من 'الراتب') ===
                    if (Helper::checkPermission(66)) {
                        $editBtn = '
                        <button title="تعديل حالة العهدة" type="button" 
                            data-status="' . $data->status . '" 
                            data-id="' . $data->id . '" 
                            id="status" 
                            class="btn btn-icon btn-light-primary me-2 mb-2 py-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#statusModal">
                            <i class="fas fa-pen"></i>
                        </button>';
                    }

                    // === 2. زر التسليم/التأكيد ===
                    if (auth()->user()->role === 'cashier' && $data->status ==='Pending') {
                        $AcceptBtn = '
                        <button title="قبول العهدة المالية" type="button" 
                            data-id="' . $data->id . '" 
                            id="deliveryBtn_' . $data->id . '" 
                            class="btn btn-icon btn-light-success me-2 mb-2 py-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#AcceptModal">
                            <i class="fas fa-check-circle fs-4"></i>
                        </button>';
                    }

                    

                    

                    $actionBtn = $editBtn . $AcceptBtn . $viewBtn ; 

                    return $actionBtn;
                })
                
                ->rawColumns(['action', 'advance_status'])
                ->make(true);
        }

        // جلب قائمة الموظفين (المتعهد عليهم) - يفضل جلب فقط من لديهم دور cashier
        $employees = User::where('role','cashier')->get();
        return view("advances.index",compact('employees'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Helper::checkPermission(18))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        // جلب قائمة الموظفين (المتعهد عليهم) - يفضل جلب فقط من لديهم دور cashier
        $employees = User::where('role','cashier')->get();
        return view('advances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Helper::checkPermission(18))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        $advance = FinancialAdvance::create([
            'user_id' => $request->user_id,
            'issued_by_user_id' => auth()->user()->id, // من هو المدير الذي سجل العهدة
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'Pending', // الحالة الافتراضية
        ]);

        // إرسال الإشعار إلى الكاشير المسؤول 
        // 1. جلب الكاشير المستهدف بالإشعار
        $cashier = User::find($request->user_id);
        
        // 2. إرسال الإشعار
        if ($cashier) {
            $cashier->notify(new NewAdvancePendingConfirmation($advance));
        }

        return redirect()->route('advances.index',[])->with([
            'success' => 'تم تسجيل العهدة المالية بنجاح.',
        ]);
        
    }

    public function confirm($id)
    {
        $advance= FinancialAdvance::findOrFail($id);
        // 1. تأكيد أن العهدة تابعة للموظف الحالي
        if ($advance->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتأكيد هذه العهدة.');
        }

        // 2. التأكد من أن الحالة الحالية هي Pending (بانتظار التأكيد)
        if ($advance->status !== 'Pending') {
            return back()->with('error', 'تم تأكيد هذه العهدة مسبقاً أو تم إغلاقها.');
        }

        // 3. تحديث الحالة
        $advance->update([
            'status' => 'Confirmed',
            'confirmation_date' => now(),
            'remaining_balance' => $advance->amount, //  قيمة الرصيد المتبقي = المبلغ الأصلي (لأنه لم يُقفل شيء بعد)
            'closed_amount' => 0.00,
        ]);

        return back()->with('success', 'تم تأكيد استلام العهدة بنجاح.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FinancialAdvance $advance)
    {
        // 1. التحقق من صلاحية الوصول للعرض
        $allowViewRoles = ['accountant', 'cashier'];
        $userRole = auth()->user()->role;

        if (!in_array($userRole, $allowViewRoles) && $advance->user_id !== auth()->id()) {
            return redirect()->back()->with("error", "لا تملك صلاحية لعرض تفاصيل هذه العهدة.");
        }

        // 2. جلب سجلات التقفيل المرتبطة بالعهدة والفواتير
        $closures = AdvanceInvoiceClosure::with('invoice')
            ->where('is_rejected', false)
            ->where('advance_id', $advance->id)
            ->latest()
            ->get();

        
        
        // 3. إرجاع بيانات العهدة وسجلات التقفيل إلى صفحة العرض
        return view('advances.show', compact('advance', 'closures'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    

}
