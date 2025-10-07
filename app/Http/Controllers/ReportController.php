<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAdvance;
use App\Models\ExpenseInvoice;
use App\Models\AdvanceInvoiceClosure;
use App\Models\Supplier; 
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use App\Http\Helper\Helper;
use Carbon\Carbon;
use ZipArchive;


class ReportController extends Controller
{

    public function showAdvanceReportView()
    {
        if(!Helper::checkPermission(27))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $users = User::select('id', 'full_name')->where('role','cashier')->get(); 
        return view('reports.advance_balance', compact('users'));
    }


    /**
     * تقرير أرصدة العهد لموظف معين خلال فترة محددة.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeeAdvanceBalanceByDate(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $userId   = $request->input('user_id');
        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        $employee = User::findOrFail($userId);

        // شرط مشترك: العهد غير المعلقة
        $advanceBaseQuery = $employee->financialAdvances()
            ->where('status', '!=', 'Pending');

        // --- 2. حساب المسلّم الكلي (Issued) ---
        $totalIssued = (clone $advanceBaseQuery)
            ->whereBetween('issue_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // --- 3. حساب المصروف الكلي (Closed) ---
        $totalClosed = (clone $advanceBaseQuery)
            ->join('advance_invoice_closures', 'financial_advances.id', '=', 'advance_invoice_closures.advance_id')
            ->whereBetween('advance_invoice_closures.closure_date', [$dateFrom, $dateTo])
            ->where('advance_invoice_closures.accountant_approved', 1)
            ->where('advance_invoice_closures.is_rejected', 0)
            ->sum('advance_invoice_closures.closed_amount');

        // --- 4. حساب الرصيد المتبقي ---
        $remainingBalance = $totalIssued - $totalClosed;

        // =======================================================
        // 5. جلب تفاصيل الحركات (Transactions)
        // =======================================================

        // أ. العهد المسلّمة
        $issuedAdvances = (clone $advanceBaseQuery)
            ->whereBetween('issue_date', [$dateFrom, $dateTo])
            ->get(['id','issue_date', 'amount', 'description'])
            ->map(function ($advance) {
                return [
                    'date'        => $advance->issue_date->toDateString(),
                    'description' => 'عهدة مسلّمة: ' . ($advance->description ?? 'لا يوجد بيان'),
                    'amount'      => (float)$advance->amount,
                    'type'        => 'issued',
                    'sort_order'  => 1,
                ];
            });

        // ب. المبالغ المقفلة
        $closedAmounts = AdvanceInvoiceClosure::query()
            ->whereHas('advance', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', '!=', 'Pending');
            })
            ->whereBetween('closure_date', [$dateFrom, $dateTo])
            ->where('accountant_approved', 1)
            ->where('is_rejected', 0)
            ->with('invoice:id,invoice_no')
            ->select('id','closure_date','closed_amount','invoice_id')
            ->get()
            ->map(function ($closure) {
                $invoiceNo   = $closure->invoice->invoice_no ?? 'غير محدد';
                $description = 'تقفيل فاتورة رقم: ' . $invoiceNo;

                return [
                    'date'        => $closure->closure_date->toDateString(),
                    'description' => $description,
                    'amount'      => (float)$closure->closed_amount,
                    'type'        => 'closed',
                    'sort_order'  => 2,
                ];
            });

        // ج. دمج وترتيب الحركات
        $transactions = $issuedAdvances
            ->merge($closedAmounts)
            ->sortByDesc('date')      // الأحدث أولاً
            ->sortBy('sort_order')    // المسلم قبل المصروف
            ->values()
            ->all();

        // --- 6. تجهيز الرد ---
        $report = [
            'employee_name' => $employee->full_name ?? $employee->name,
            'period'        => [
                'from' => $dateFrom->toDateString(),
                'to'   => $dateTo->toDateString(),
            ],
            'total_issued'  => (float)$totalIssued,
            'total_closed'  => (float)$totalClosed,
            'net_balance'   => (float)$remainingBalance,
        ];

        return response()->json([
            'success'      => true,
            'report'       => $report,
            'transactions' => $transactions,
        ]);
    }



    /**
     * يعرض واجهة تقرير ملخص العهد لجميع الموظفين.
     *
     * @return \Illuminate\View\View
     */
    public function showAdvanceSummaryAllView()
    {
        if(!Helper::checkPermission(28))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");


        // بما أن هذا التقرير لا يحتاج إلى قائمة موظفين في حقول البحث، 
        // لا نحتاج لجلب أي بيانات، فقط نرسل إلى النموذج (View).
        return view('reports.advance_summary_all'); 
        // تأكد أن اسم الملف هو advance_summary_all.blade.php
    }


    /**
     * يجلب تقرير ملخص العهد لجميع الموظفين ضمن فترة زمنية محددة.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllEmployeesAdvanceBalanceSummary(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();

        // 2. جلب جميع الموظفين الذين لديهم حركات عهد
        $employeesWithAdvances = User::whereHas('financialAdvances', function($query) use ($dateFrom, $dateTo) {
            $query->where('status', '!=', 'Pending')
                  ->whereBetween('issue_date', [$dateFrom, $dateTo])
                  ->orWhereHas('closures', function($q) use ($dateFrom, $dateTo) {
                      $q->whereBetween('closure_date', [$dateFrom, $dateTo]);
                  });
        })->get(['id', 'name', 'full_name']);

        $summaryReports = [];
        $grandTotalIssued = 0;
        $grandTotalClosed = 0;

        // 3. حساب الملخص لكل موظف على حدة
        foreach ($employeesWithAdvances as $employee) {
            
            $advanceBaseQuery = $employee->financialAdvances()->where('status', '!=', 'Pending');

            // أ. المسلّم (Issued) ضمن الفترة
            $totalIssued = (clone $advanceBaseQuery)
                ->whereBetween('issue_date', [$dateFrom, $dateTo])
                ->sum('amount');

            // ب. المصروف (Closed) ضمن الفترة
            $totalClosed = (clone $advanceBaseQuery)
                ->join('advance_invoice_closures', 'financial_advances.id', '=', 'advance_invoice_closures.advance_id')
                ->whereBetween('advance_invoice_closures.closure_date', [$dateFrom, $dateTo])
                ->where('advance_invoice_closures.accountant_approved', 1)
                ->where('advance_invoice_closures.is_rejected', 0)
                ->sum('advance_invoice_closures.closed_amount');

            // إذا لم يكن هناك حركة مسلّمة أو مصروفة خلال الفترة، ننتقل للموظف التالي
            if ($totalIssued == 0 && $totalClosed == 0) {
                continue;
            }

            // ج. الرصيد الصافي
            $netBalance = $totalIssued - $totalClosed;

            // د. تجميع التقارير
            $summaryReports[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name ?? $employee->name,
                'total_issued' => (float)$totalIssued,
                'total_closed' => (float)$totalClosed,
                'net_balance' => (float)$netBalance,
            ];
            
            // هـ. حساب الإجمالي العام
            $grandTotalIssued += $totalIssued;
            $grandTotalClosed += $totalClosed;
        }

        // 4. تجهيز الرد
        $grandNetBalance = $grandTotalIssued - $grandTotalClosed;

        return response()->json([
            'success' => true,
            'period' => [
                'from' => $dateFrom->toDateString(),
                'to' => $dateTo->toDateString(),
            ],
            'grand_totals' => [
                'total_issued' => (float)$grandTotalIssued,
                'total_closed' => (float)$grandTotalClosed,
                'net_balance' => (float)$grandNetBalance,
            ],
            'reports' => $summaryReports,
        ]);
    }


    public function ConfirmedClosureReport(Request $request)
    {

       
        if (!Helper::checkPermission(29)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى هذا التقرير");
        }

        if ($request->ajax()) {
        
            // 1. الاستعلام الأساسي مع العلاقات المطلوبة
            $query = AdvanceInvoiceClosure::with([
                'advance.recipient', // العهدة ومستلمها (الكاشير)
                'invoice.supplier',  // الفاتورة وموردها
                'closer'             // المحاسب الذي قام بالتقفيل
            ])
            // 2. شرط "التقفيل المعتمد"
            ->where('accountant_approved', 1) 
            ->where('is_rejected', 0)
            ->latest('closure_date');
    
            // 4. تطبيق الفلاتر (كما كان سابقاً)
    
            // فلترة الكاشير (المستلم)
            if ($request->filled('user_id')) {
                $query->whereHas('advance', function($q) use ($request) {
                    // الفلترة تتم على الموظف المستلم للعهدة
                    $q->where('user_id', $request->user_id); 
                });
            }

            if ($request->filled('supplier_id')) {
                // نستخدم whereHas للبحث في المورد عبر علاقة الفاتورة
                $query->whereHas('invoice', function($q) use ($request) {
                    $q->where('supplier_id', $request->supplier_id); 
                });
            }
    
            // فلترة التاريخ (على تاريخ التقفيل الفعلي)
            if ($request->filled('date_from') && $request->filled('date_to')) {
                try {
                    $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                    $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
                    // نستخدم حقل closure_date من جدول AdvanceInvoiceClosure
                    $query->whereBetween('closure_date', [$dateFrom, $dateTo]); 
                } catch (\Exception $e) {
                    // تجاهل خطأ التاريخ
                }
            }
            
            // 5. إعداد DataTables
            $data = $query;
            
            return DataTables::of($data)
                ->addIndexColumn()
                
                // 1. بيانات العهدة والكاشير (المستلم)
                ->addColumn('cashier_name', function ($closure) {
                    return $closure->advance->recipient->full_name ?? 'N/A';
                })
                ->addColumn('advance_id_num', function ($closure) {
                    return $closure->advance_id;
                })
                //  عمود إضافي: وصف العهدة
                ->addColumn('advance_description', function ($closure) {
                    return $closure->advance->description ?? '-';
                })
                
                // 2. بيانات الفاتورة والمصروف
                //  عمود إضافي: رقم الفاتورة 
                ->addColumn('invoice_number', function ($closure) {
                    return $closure->invoice->invoice_no ?? 'N/A';
                })
                //  عمود إضافي: تاريخ الفاتورة 
                ->addColumn('invoice_date', function ($closure) {
                    return $closure->invoice->invoice_date ? Carbon::parse($closure->invoice->invoice_date)->format('Y-m-d') : '-';
                })
                ->addColumn('item_details', function ($closure) {
                    return $closure->invoice->description ?? 'لا يوجد وصف';
                })
                ->addColumn('supplier_name', function ($closure) {
                    return $closure->invoice->supplier->name_ar ?? 'لا يوجد مورد'; 
                })

                // 3. بيانات التقفيل والاعتماد
                ->addColumn('invoice_amount', function ($closure) {
                    return number_format($closure->closed_amount, 2);
                })
                ->addColumn('closure_date', function ($closure) {
                    return Carbon::parse($closure->closure_date)->format('Y-m-d');
                })
                //  عمود إضافي: المحاسب المعتمد 
                ->addColumn('approver_name', function ($closure) {
                    // نستخدم علاقة 'closer' التي تربط بـ 'closed_by_user_id'
                    return $closure->closer->full_name ?? 'N/A';
                })
                ->addColumn('action', function ($closure) {
                    $invoiceId = $closure->invoice_id;
            
                    // نفترض أن مسار العرض هو 'invoices.show' 
                    // ويجب أن يكون مساراً موجوداً وموصولاً بدالة عرض الفاتورة في كنترولر الفواتير
                    $showUrl = route('invoices.show', $invoiceId);
            
                    $html = '<a href="'.$showUrl.'" target="_blank" class="btn btn-sm btn-info me-2" title="عرض الفاتورة">';
                    $html .= '<i class="ki-duotone ki-eye fs-4"></i>'; // أيقونة العرض
                    $html .= '</a>';
                    
                    return $html;
                })
                
                ->rawColumns(['item_details', 'supplier_name', 'advance_description','action']) 
                ->make(true);
        }

    

        // عرض الواجهة الرسومية
        $employees = User::whereIn('role', ['cashier'])->get(); 
        $suppliers = Supplier::get();
        return view("reports.closure_report", compact('employees','suppliers'));
    }

    public function downloadConfirmedClosureAttachments(Request $request)
    {
        if (!Helper::checkPermission(29)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات لتنزيل المرفقات");
        }

        // نفس الاستعلام السابق مع الفلاتر
        $query = AdvanceInvoiceClosure::with(['advance', 'invoice'])
            ->where('accountant_approved', 1)
            ->where('is_rejected', 0);

        if ($request->filled('user_id')) {
            $query->whereHas('advance', fn($q) => $q->where('user_id', $request->user_id));
        }
        if ($request->filled('supplier_id')) {
            $query->whereHas('invoice', fn($q) => $q->where('supplier_id', $request->supplier_id));
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->whereBetween('closure_date', [$dateFrom, $dateTo]); 
            } catch (\Exception $e) {}
        }

        $closures = $query->get();

        if ($closures->isEmpty()) {
            return back()->with('error', 'لا توجد فواتير تحتوي على مرفقات في هذه الفترة/الفلاتر.');
        }

        $zipFileName = 'attachments_' . now()->format('Ymd_His') . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($closures as $closure) {
                if ($closure->invoice && $closure->invoice->file_path) {
                    // المسار الصحيح للملف
                    $filePath = storage_path('app/public/' . $closure->invoice->file_path);
                    if (file_exists($filePath)) {
                        // 💡 اسم الملف داخل الـ ZIP = رقم العهدة + رقم الفاتورة
                        $advanceId  = $closure->advance->id ?? 'NA';
                        $invoiceNo  = $closure->invoice->invoice_no ?? 'no_number';
                        $extension  = pathinfo($filePath, PATHINFO_EXTENSION);

                        $fileNameInZip = "advance_{$advanceId}_invoice_{$invoiceNo}.{$extension}";

                        $zip->addFile($filePath, $fileNameInZip);
                    }
                }
            }
            $zip->close();
        }

        if (!file_exists($zipFilePath)) {
            return back()->with('error', 'فشل في إنشاء ملف المرفقات.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }


}
