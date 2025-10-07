<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAdvance;
use App\Models\User;
use App\Models\AdvanceInvoiceClosure;
use App\Models\ExpenseInvoice;
use App\Models\Supplier;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;



class ExpenseInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Helper::checkPermission(21)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }


        if ($request->ajax()) {

            // 1. استخدام auth()->id() أكثر نظافة وأماناً
            $userId = auth()->id();

            $query = ExpenseInvoice::with('user','supplier','closures')
                        ->orderByRaw("FIELD(status, 'Pending Review', 'Approved', 'Rejected')")
                        ->orderBy('created_at', 'asc')
                        ;

            // 2. تبسيط التحقق من الأدوار باستخدام مصفوفة
            $fullAccessRoles = ['admin', 'accountant'];
            $userRole = auth()->user()->role;
            
            // إذا كان الدور ليس ضمن الأدوار ذات الصلاحية الكاملة (مثل الكاشير):
            if (!in_array($userRole, $fullAccessRoles)) {
                // تطبيق قيد جلب العُهد الخاصة بالمستخدم الحالي فقط
                $query->where('user_id', $userId);

            }
            
            if ($request->filled('date_from') && $request->filled('date_to')) {
                // كلا التاريخين موجودين → فلترة بينهما
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
            
            } elseif ($request->filled('date_from')) {
                // فقط من تاريخ موجود
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
                $query->where('invoice_date', '>=', $dateFrom);
            
            } elseif ($request->filled('date_to')) {
                // فقط إلى تاريخ موجود
                $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->where('invoice_date', '<=', $dateTo);
            }

            if ($request->filled('closure_status')) {
                $closureStatus = $request->input('closure_status');
            
                if ($closureStatus == 'open') {
                    $query->whereDoesntHave('closures');
                } elseif ($closureStatus == 'closed_pending') {
                    $query->whereHas('closures', function($q) {
                        $q->where('accountant_approved', false);
                    });
                } elseif ($closureStatus == 'closed_approved') {
                    $query->whereHas('closures', function($q) {
                        $q->where('accountant_approved', true);
                    });
                }
            }
            
            $data = $query;
            
            return DataTables::of($data)
                ->addIndexColumn()
                
                ->editColumn('supplier.name_ar', function($data) {
                    return $data->supplier ? $data->supplier->name_ar : '';
                })
                ->addColumn('invoice_status', function ($data) {
                    // 3. تنظيم عرض حالة العهدة باستخدام خريطة (Map) للحالات والألوان
                    $classMap = [
                        'Pending Review'          => 'light-primary', // قيد الانتظار
                        'Approved'        => 'light-success', // تم التأكيد
                        'Rejected'        => 'light-danger', // مسدد جزئياً
                    ];

                    $class = $classMap[$data->status] ?? 'light-secondary'; // لون افتراضي للحالات غير المعرفة
                    
                    return '<span class="label label-inline label-' . $class . ' font-weight-bold">' . $data->status . '</span>';
                })

                ->addColumn('closure_status', function($data) {
                    // التحقق من وجود إغلاق
                    $isClosed = $data->closures()->exists();
                    $isApproved = $data->closures()->where('accountant_approved', true)->exists();
                
                    if($isClosed && $isApproved) {
                        $label = '<span class="label label-inline label-success font-weight-bold">مغلقة وموافق عليها</span>';
                    } elseif ($isClosed && !$isApproved) {
                        $label = '<span class="label label-inline label-warning font-weight-bold">مغلقة بدون موافقة</span>';
                    } else {
                        $label = '<span class="label label-inline label-secondary font-weight-bold">غير مغلقة</span>';
                    }
                
                    return $label;
                })
                
                ->addColumn('action', function ($data) {
                    $editBtn = '';
                    $downloadBtn = '';
                    $deleteBtn = '';
                    $viewBtn = '';

                    // تعريف الأدوار المسموح لها بالعرض (Accountant, Cashier)
                    $allowViewRoles = ['accountant', 'cashier'];

                    if (in_array(auth()->user()->role, $allowViewRoles)) {
                        $viewBtn = '
                        <a href="' . route("invoices.show", $data->id) . '" 
                            class="btn btn-icon btn-light-info me-2 mb-2 py-3"
                            title="عرض تفاصيل الفاتورة"
                            target="_blank">
                            <i class="fas fa-file-alt"></i>
                        </a>';
                    }

                    /*
                        <a href="' . route("invoices.download", $data->id) . '" 
                            class="btn btn-icon btn-light-info me-2 mb-2 py-3" 
                            title="تحميل المرفق" target="_blank">
                            <i class="fas fa-download"></i>
                            
                        </a>
                    */
                    if ($data->file_path) {
                        $downloadBtn = '
                        
                        <a href="' . route("invoices.view", $data->id) . '" 
                            class="btn btn-icon btn-light-primary me-2 mb-2 py-3" 
                            title="عرض المرفق" target="_blank">
                            <i class="fa fa-eye"></i>
                        </a>                   
                        ';
                    }

                    //  هل الفاتورة تم إغلاقها ضمن عهدة؟ 
                    $is_closed_in_advance = $data->closures()->where('accountant_approved', true)->exists();
                    
                    if(!$is_closed_in_advance){
                        $editBtn = '
                        <a href="'.route("invoices.edit",$data->id).'" class="btn btn-icon btn-light-success me-2 mb-2 py-3" ><i class="fa fa-pen"></i></a>

                        ';
                    }

                    
                    if ($data->status !== 'Rejected') {
                        $deleteBtn = '<button type="button" 
                                            class="btn btn-icon btn-light-danger me-2 mb-2 py-3" 
                                            onclick="rejectInvoice('.$data->id.')" 
                                            title="رفض الفاتورة">
                                            <i class="fas fa-trash"></i>
                                        </button>';
                    }


                    

                    
                    

                    $actionBtn = $editBtn . $viewBtn .  $downloadBtn. $deleteBtn ; 

                    return $actionBtn;
                })
                
                ->rawColumns(['action','supplier.name_ar', 'invoice_status','closure_status'])
                ->make(true);
        }

        // جلب قائمة الموظفين (المتعهد عليهم) - يفضل جلب فقط من لديهم دور cashier
        $employees = User::where('role','cashier')->get();
        return view("invoices.index",compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Helper::checkPermission(22)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        // يجب أن نربط الفاتورة بعهدة مؤكدة وغير مغلقة
        $confirmedAdvancess = auth()->user()->financialAdvances()->where('status', 'Confirmed')->get();
        $suppliers = Supplier::all();
        $branches = Branch::all(); // جلب الفروع
        return view('invoices.create', compact('confirmedAdvancess','suppliers','branches'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Helper::checkPermission(22)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }
        //dd($request);
        $request->validate([
            'invoice_type' => 'required|in:Tax_Invoice,Invoice_with_Attachments,Invoice_without_Attachments',
            //'invoice_no' => 'required_if:invoice_type,Tax_Invoice,Invoice_with_Attachments|integer|nullable',
            // 'supplier_id' اختياري إذا لم يكن 'فاتورة ضريبية'
            //'supplier_id'  => 'nullable|required_if:invoice_type,Tax_Invoice|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'invoice_date' => 'required|date',
            // الملاحظات إجبارية فقط إذا كانت فاتورة بدون مرفقات
            'description' => 'required_if:invoice_type,Invoice_without_Attachments|string|nullable',
            // المرفق إجباري في حالتين
            'file' => 'required_if:invoice_type,Tax_Invoice,Invoice_with_Attachments|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // تحقق جديد لتخصيص الفروع:
            'branch_type' => 'required|in:single,multiple,general',
            'branch_id' => 'required_if:branch_type,single,multiple|array|nullable',
            'branch_id.*' => 'nullable|exists:branches,id', // تحقق من أن كل فرع موجود
        ]);

        //  تحديد رقم الفاتورة حسب النوع
        if ($request->invoice_type === 'Invoice_without_Attachments') {
            // تسلسل خاص لفواتير بدون مرفقات
            $lastInvoice = ExpenseInvoice::where('invoice_type', 'Invoice_without_Attachments')
                ->orderBy('invoice_no', 'desc')
                ->first();

            $invoiceNo = $lastInvoice ? $lastInvoice->invoice_no + 1 : 1000;
        } else {
            // رقم الفاتورة مطلوب إذا كانت ضريبية أو مع مرفقات
            $request->validate([
                'invoice_no' => 'required|integer',
                'supplier_id'  => 'nullable|required_if:invoice_type,Tax_Invoice|exists:suppliers,id',
            ]);

            // فحص تكرار رقم الفاتورة لنفس المورد
            $exists = ExpenseInvoice::where('invoice_no', $request->invoice_no)
                ->where('supplier_id', $request->supplier_id)
                ->exists();

            if ($exists) {
                return back()
                    ->with('error', "رقم الفاتورة {$request->invoice_no} موجود مسبقاً لهذا المورد.")
                    ->withInput();
            }

            $invoiceNo = $request->invoice_no;
        }

        
    


        // نحدد مسار الملف بقيمة أولية null
        $filePath = null;

        try {
            DB::beginTransaction();

            // 1. معالجة تحميل الملف: يتم تخزين الملف فقط إذا كان موجوداً في الطلب
            if ($request->hasFile('file')) {
                // يتم تخزين الملف في 'storage/app/public/invoices'
                $filePath = $request->file('file')->store('invoices', 'public');
            }

            // 2. تحديد supplier_id بقيمة null إذا لم يتم إرساله
            $supplierId = $request->supplier_id ?? null;

            // 3. تحديد ما إذا كانت فاتورة عامة
            $isGeneralExpense = ($request->branch_type === 'general');
          
            // 4. حفظ بيانات الفاتورة
            $invoice = ExpenseInvoice::create([
                'invoice_type' => $request->invoice_type,
                'invoice_no' => $invoiceNo,
                'user_id' => auth()->id(),
                'supplier_id' => $supplierId ,
                'amount' => $request->amount,
                'invoice_date' => $request->invoice_date,
                'description' => $request->description,
                'file_path' => $filePath,
                'status' => 'Approved', // الحالة الافتراضية
                'is_general_expense' => $isGeneralExpense,
                // used_amount سيبقى 0.00 افتراضياً كما في الهجرة
            ]);

            // 5. ربط الفاتورة بالفروع إذا لم تكن عامة
            if (!$isGeneralExpense && $request->filled('branch_id')) {
                $invoice->branches()->sync($request->branch_id);
            }

            DB::commit(); // تأكيد المعاملة

            return redirect()->route('invoices.index')->with('success', 'تم تسجيل الفاتورة بنجاح. في انتظار المراجعة.');

        } catch (\Exception $e) {
            DB::rollBack(); // التراجع في حالة الفشل
            // حذف الملف إذا فشل حفظ سجل قاعدة البيانات
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'فشل تسجيل الفاتورة: ' . $e->getMessage())->withInput();
        }
    }

    // وظيفة لتحميل المرفق مباشرة
    public function downloadFile(ExpenseInvoice $invoice)
    {

        // التحقق من وجود الملف
        if (!$invoice->file_path || !Storage::disk('public')->exists($invoice->file_path)) {
            return redirect()->back()->with("error", "لا يوجد مرفق لهذه الفاتورة.");
        }
    

        // تحميل الملف
        $filename = 'Invoice_' . $invoice->id . '.' . pathinfo($invoice->file_path, PATHINFO_EXTENSION);
        
        // Get the MIME type dynamically
        $mimeType = Storage::disk('public')->mimeType($invoice->file_path);

        return response()->download(
            Storage::disk('public')->path($invoice->file_path), 
            $filename, 
            [
                'Content-Type' => $mimeType,
                // 'attachment' forces the browser to download
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function viewFile(ExpenseInvoice $invoice)
    {
        // 1. Check file existence
        if (!$invoice->file_path || !Storage::disk('public')->exists($invoice->file_path)) {
            return redirect()->back()->with("error", "لا يوجد مرفق لهذه الفاتورة.");
        }

        // 2. Get the file content and determine the MIME type
        $fileContents = Storage::disk('public')->get($invoice->file_path);
        $mimeType = Storage::disk('public')->mimeType($invoice->file_path);

        // 3. Return the response with the correct headers (Inline)
        return Response::make($fileContents, 200, [
            'Content-Type' => $mimeType,
            // 'inline' tells the browser to display it rather than download
            'Content-Disposition' => 'inline; filename="' . basename($invoice->file_path) . '"'
        ]);
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseInvoice $invoice)
    {
     
        // التحقق من الصلاحية: يجب أن يكون المستخدم إما مالك الفاتورة أو محاسباً
        if (auth()->id() !== $invoice->user_id && auth()->user()->role !== 'accountant') {
            abort(403, 'غير مصرح لك بمشاهدة تفاصيل هذه الفاتورة.');
        }
        
        // تحميل علاقة المستخدم الذي قدم الفاتورة (للعرض)
        $invoice->load('user','branches');
        $branch_names = $invoice->branches?->pluck('name_ar')->implode(', ') ?? 'غير محدد';
        

        return view('invoices.show', compact('invoice','branch_names'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Helper::checkPermission(22))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $invoice=ExpenseInvoice::findOrFail($id);

        $suppliers = Supplier::all();
        $branches = Branch::all();
        return view("invoices.edit",compact("invoice",'suppliers','branches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseInvoice $invoice)
    {
        if (!Helper::checkPermission(22)) {
            return redirect()->back()->with("error", "لايوجد صلاحيات للوصول الى النموذج المطلوب");
        }

        $request->validate([
            'invoice_type' => 'required|in:Tax_Invoice,Invoice_with_Attachments,Invoice_without_Attachments',
            'invoice_no' => 'required|integer',
            'supplier_id'  => 'nullable|required_if:invoice_type,Tax_Invoice|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'invoice_date' => 'required|date',
            'description' => 'required_if:invoice_type,Invoice_without_Attachments|string|nullable',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
             // تحقق جديد لتخصيص الفروع:
             'branch_type' => 'required|in:single,multiple,general',
             'branch_id' => 'required_if:branch_type,single,multiple|array|nullable',
             'branch_id.*' => 'nullable|exists:branches,id', // تحقق من أن كل فرع موجود
        ]);

        // فحص هل الموظف الحالي لة صلاحية كاشير
        $user_id = auth()->id();
        $userRole = auth()->user()->role;
        $fullAccessRoles = ['cashier'];

        //  هل المستخدم الحالي كاشير؟
        $is_cashier = in_array($userRole, $fullAccessRoles) ?? false;
        
        //  هل المستخدم الحالي هو منشئ الفاتورة؟ 
        $is_creator = ($invoice->user_id === auth()->id()); 

        //  هل الفاتورة تم إغلاقها ضمن عهدة؟ 
        $is_closed_in_advance = $invoice->closures()->where('accountant_approved', true)->exists();

        //  هل الفاتورة مرفوضة من المحاسب؟ 
        $is_rejected = $invoice->closures()->where('is_rejected', true)->exists();

        // إذا كان المستخدم كاشير (ويجب أن يكون أيضاً هو المنشئ)
        // لا يسمح له بالتعديل إلا إذا كانت الفاتورة مرفوضة وغير مغلقة في عهدة.
        
        if ($is_cashier && $is_creator) {
            // مسموح للكاشير التعديل فقط في الحالات التالية:
            // - إذا كانت الفاتورة **مرفوضة** (ليقوم بتصحيحها وإعادة إرسالها).
            // - إذا كانت الفاتورة **لم تغلق بعد في عهدة**.
            if ($is_closed_in_advance) {
                return back()->with('error', 'لا يمكن تعديل الفاتورة! تم إغلاقها بالفعل ضمن عهدة ولا يمكن تغييرها.');
            }

            if (!$is_rejected) {
                // إذا لم تكن مرفوضة، نفحص ما إذا كانت في مرحلة المراجعة (لم ترفض ولم تغلق)
                
            }
            
        } elseif ($is_cashier && !$is_creator) {
            // الكاشير ليس منشئ الفاتورة، لا يمكنه التعديل
            return back()->with('error', 'لا تملك الصلاحية لتعديل فواتير أنشأها مستخدم آخر.');
        }

        // يجب منعهم من التعديل إذا تم الإغلاق أيضاً
        if (!$is_cashier && $is_closed_in_advance) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة! تم إغلاقها ضمن عهدة، والتعديل ممنوع بعد الإغلاق.');
        }

        // فحص تكرار رقم الفاتورة لنفس المورد مع استثناء السجل الحالي
        $exists = ExpenseInvoice::where('invoice_no', $request->invoice_no)
            ->where('supplier_id', $request->supplier_id)
            ->where('id', '!=', $invoice->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "رقم الفاتورة {$request->invoice_no} موجود مسبقاً لهذا المورد.")->withInput();
        }

        $filePath = $invoice->file_path;

        try {
            DB::beginTransaction();

            // معالجة تحميل الملف
            if ($request->hasFile('file')) {
                // حذف الملف القديم إذا موجود
                if ($invoice->file_path) {
                    Storage::disk('public')->delete($invoice->file_path);
                }
                
                $filePath = $request->file('file')->store('invoices', 'public');
            }

            // تحديد ما إذا كانت فاتورة عامة
            $isGeneralExpense = ($request->branch_type === 'general');

            $invoice->update([
                'invoice_type' => $request->invoice_type,
                'invoice_no' => $request->invoice_no,
                'supplier_id' => $request->supplier_id,
                'amount' => $request->amount,
                'invoice_date' => $request->invoice_date,
                'description' => $request->description,
                'file_path' => $filePath,
                'is_general_expense' => $isGeneralExpense,
            ]);

            // ربط الفاتورة بالفروع إذا لم تكن عامة
            if (!$isGeneralExpense && $request->filled('branch_id')) {
                $invoice->branches()->sync($request->branch_id);
            } else {
                 // إذا كانت عامة أو لم يتم إرسال فروع، نفصل جميع الروابط القديمة
                $invoice->branches()->sync([]);
            }
            
            DB::commit();

            return redirect()->route('invoices.index')->with('success', 'تم تحديث الفاتورة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل تحديث الفاتورة: ' . $e->getMessage())->withInput();
        }
    }


    public function reject(ExpenseInvoice $invoice)
    {
        if (!Helper::checkPermission(22)) { // صلاحية رفض الفاتورة
            return response()->json(['error' => 'لايوجد صلاحيات'], 403);
        }

        // فحص هل الموظف الحالي لة صلاحية كاشير
        $user_id = auth()->id();
        $userRole = auth()->user()->role;
        $fullAccessRoles = ['cashier'];

        //  هل المستخدم الحالي كاشير؟
        $is_cashier = in_array($userRole, $fullAccessRoles) ?? false;
        
        //  هل المستخدم الحالي هو منشئ الفاتورة؟ 
        $is_creator = ($invoice->user_id === auth()->id()); 

        //  هل الفاتورة تم إغلاقها ضمن عهدة؟ 
        $is_closed_in_advance = $invoice->closures()->where('accountant_approved', true)->exists();

        //  هل الفاتورة مرفوضة من المحاسب؟ 
        $is_rejected = $invoice->closures()->where('is_rejected', true)->exists();

        // إذا كان المستخدم كاشير (ويجب أن يكون أيضاً هو المنشئ)
        // لا يسمح له بالتعديل إلا إذا كانت الفاتورة مرفوضة وغير مغلقة في عهدة.
        
        if ($is_cashier && $is_creator) {
            // مسموح للكاشير التعديل فقط في الحالات التالية:
            // - إذا كانت الفاتورة **مرفوضة** (ليقوم بتصحيحها وإعادة إرسالها).
            // - إذا كانت الفاتورة **لم تغلق بعد في عهدة**.
            if ($is_closed_in_advance) {
                return response()->json([
                    'error' => 'لا يمكن رفض الفاتورة! تم إغلاقها بالفعل ضمن عهدة ولا يمكن تغييرها.'
                ], 400);
            }

            if (!$is_rejected) {
                // إذا لم تكن مرفوضة، نفحص ما إذا كانت في مرحلة المراجعة (لم ترفض ولم تغلق)
                
            }
            
        } elseif ($is_cashier && !$is_creator) {
            // الكاشير ليس منشئ الفاتورة، لا يمكنه التعديل
            return response()->json([
                'error' => 'لا تملك الصلاحية لرفض فواتير أنشأها مستخدم آخر.'
            ], 403);
        }

        // يجب منعهم من التعديل إذا تم الإغلاق أيضاً
        if (!$is_cashier && $is_closed_in_advance) {
            return response()->json([
                'error' => 'لا يمكن رفض هذه الفاتورة! تم إغلاقها ضمن عهدة.'
            ], 400);
        }


        try {
            $invoice->status = 'Rejected';
            $invoice->save();

            return response()->json(['success' => 'تم رفض الفاتورة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل رفض الفاتورة: ' . $e->getMessage()], 500);
        }
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
