@extends('layouts.app_admin')
@section('title','تقفيل العهد المالية')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="text-align: right;">
                <div class="card-body py-1">
                    <div class="mb-13 mt-5 text-start">
                        <h1 class="mb-3">@yield('title')</h1>
                        <div class="text-gray-400 fw-bold fs-5">
                            قم بربط عهدة مالية معتمدة برصيد متبقٍ بفاتورة مصاريف مُعتمدة أو أكثر.
                        </div>
                    </div>
                    
                    {{-- ********** ملخص الأرصدة المتاحة ********** --}}
                    <div class="row mb-8 mt-5">
                        <div class="col-md-6">
                            <div class="alert bg-light-primary border border-primary border-dashed d-flex align-items-center py-5">
                                <i class="fas fa-wallet fs-2x text-primary me-4"></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">إجمالي أرصدة العهد المتاحة للتقفيل</h4>
                                    <span class="fs-2x fw-bold text-dark">{{ number_format($totalAvailableAdvanceAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert bg-light-success border border-success border-dashed d-flex align-items-center py-5">
                                <i class="fas fa-file-invoice fs-2x text-success me-4"></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-success">إجمالي مبالغ الفواتير المتاحة للتقفيل</h4>
                                    <span class="fs-2x fw-bold text-dark">{{ number_format($totalAvailableInvoiceAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- **************************************************** --}}

                    <form method="POST" action="{{ route('closures.process') }}" id="manual-closure-form">
                    @csrf
                    @method('POST')
                    <div class="row">
                        {{-- اختيار العهدة --}}
                        <div class="col-md-12 mb-4"> 
                            <label for="advance_id" class="form-label fw-bold">
                                <i class="fas fa-wallet text-primary"></i> اختر العهدة للتقفيل
                            </label>
                            <select class="form-select @error('advance_id') is-invalid @enderror"
                                        id="advance_id" name="advance_id" required data-control="select2" data-placeholder="-- اختر عهدة --">
                                <option value="" data-remaining="0">-- اختر عهدة --</option>
                                @foreach($availableAdvances as $advance)
                                    <option value="{{ $advance->id }}" 
                                            data-remaining="{{ $advance->remaining_balance }}"
                                            {{ old('advance_id') == $advance->id ? 'selected' : '' }}>
                                        [ID: {{ $advance->id }}] - {{ $advance->description }}
                                        (المتبقي: {{ number_format($advance->remaining_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('advance_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="alert alert-info py-2 px-3 mt-2 d-none text-dark" id="advance-info">
                                <strong>💰 الرصيد المتبقي للعهدة:</strong> <span id="current-advance-remaining" class="fs-4 fw-bold">0.00</span>
                            </div>
                        </div>

                        {{-- **************************************************** --}}
                        {{-- القسم الجديد: قائمة الفواتير لاختيار المبلغ المقفل لكل فاتورة --}}
                        {{-- **************************************************** --}}
                        <div class="col-md-12 mb-6" id="invoices-selection-section" style="display: none;">
                            <h4 class="mb-4 mt-4">
                                <i class="fas fa-file-invoice text-success"></i> حدد مبالغ التقفيل من الفواتير المتاحة:
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 gy-5">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800">
                                            <th># الفاتورة</th>
                                            <th>وصف الفاتورة</th>
                                            <th class="text-end">المتاح للتقفيل</th>
                                            <th class="text-end" style="min-width: 150px;">المبلغ المقفل</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoices-list">
                                        {{-- يتم عرض الفواتير الموجودة في الباك-إند هنا --}}
                                        @foreach($availableInvoices as $index => $invoice)
                                            <tr data-invoice-id="{{ $invoice->id }}"
                                                data-available-amount="{{ $invoice->amount - $invoice->used_amount }}">
                                                <td>{{ $invoice->id }}</td>
                                                <td>{{ $invoice->description }}</td>
                                                <td class="text-end fw-bold text-success">
                                                    {{ number_format($invoice->amount - $invoice->used_amount, 2) }}
                                                </td>
                                                <td class="text-end">
                                                    <input type="number" step="0.01" min="0.01" 
                                                        max="{{ $invoice->amount - $invoice->used_amount }}"
                                                        class="form-control form-control-sm text-end invoice-closed-amount"
                                                        placeholder="0.00" value=""> 
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold fs-5 text-dark bg-light-warning">
                                            <td colspan="3" class="text-end">
                                                إجمالي المبلغ المقفل من الفواتير:
                                            </td>
                                            <td class="text-end">
                                                <span id="total-closed-invoices-amount">0.00</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="advance-exceed-warning" class="text-danger fw-bold mt-2 d-none">
                                ⚠️ **تجاوز الرصيد:** إجمالي المبلغ المقفل (<span id="total-closed-invoices-amount-warning">0.00</span>) يتجاوز الرصيد المتبقي للعهدة.
                            </div>
                            <div id="min-amount-warning" class="text-danger fw-bold mt-2 d-none">
                                ⚠️ **خطأ:** يجب إدخال مبلغ تقفيل واحد على الأقل.
                            </div>
                        </div>
                        {{-- **************************************************** --}}
                        
                    </div>

                    {{-- أزرار --}}
                    <div class="d-flex justify-content-start gap-3 mt-5">
                        <button type="submit" form="manual-closure-form" class="btn btn-primary btn-lg" id="submit-manual-closure">
                            <i class="fas fa-check-circle"></i> تنفيذ التقفيل اليدوي
                        </button>
                        
                        {{-- زر التقفيل التلقائي --}}
                        @if($availableAdvances->count() > 0 && $availableInvoices->count() > 0)
                            <button type="submit" form="auto-closure-form" class="btn btn-success btn-lg" id="auto-closure-button">
                                <i class="fas fa-magic"></i> تقفيل تلقائي للجميع
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                تقفيل تلقائي (لا توجد أرصدة كافية)
                            </button>
                        @endif
                        
                        <button type="reset" class="btn btn-light btn-lg">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </button>
                    </div>
                </form>

                {{-- نموذج منفصل للتقفيل التلقائي (مخفي) --}}
                <form id="auto-closure-form" method="POST" action="{{ route('closures.auto.process') }}" style="display:none;">
                    @csrf
                </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    const advanceSelect = document.getElementById('advance_id');
    const advanceInfo = document.getElementById('advance-info');
    const currentAdvanceRemainingSpan = document.getElementById('current-advance-remaining');
    const invoicesSelectionSection = document.getElementById('invoices-selection-section');
    const totalClosedInvoicesAmountSpan = document.getElementById('total-closed-invoices-amount');
    const advanceExceedWarning = document.getElementById('advance-exceed-warning');
    const minAmountWarning = document.getElementById('min-amount-warning');
    const totalClosedInvoicesAmountWarningSpan = document.getElementById('total-closed-invoices-amount-warning');
    const manualClosureForm = document.getElementById('manual-closure-form');
    const submitButton = document.getElementById('submit-manual-closure');

    let currentAdvanceRemaining = 0;

    /**
     * وظيفة جديدة: ملء حقول المبلغ المقفل تلقائياً
     * بالحد الأقصى المتاح لكل فاتورة، مع الالتزام برصيد العهدة.
     */
    function autoFillAmounts() {
        if (!advanceSelect.value) return;

        const amountInputs = document.querySelectorAll('.invoice-closed-amount');
        let remainingAdvance = currentAdvanceRemaining;
        
        // أولاً: نقوم بتصفير جميع الحقول قبل التوزيع الجديد
        amountInputs.forEach(input => input.value = '');

        // ثانياً: التوزيع التلقائي
        amountInputs.forEach(input => {
            const row = input.closest('tr');
            const maxAvailable = parseFloat(row.dataset.availableAmount) || 0;
            
            if (maxAvailable > 0) {
                let amountToClose = 0;
                
                // المبلغ المقفل هو الحد الأدنى بين: المبلغ المتاح في الفاتورة، والمتبقي من العهدة
                if (remainingAdvance > 0) {
                    amountToClose = Math.min(maxAvailable, remainingAdvance);
                }
                
                if (amountToClose > 0.001) {
                    // تعيين القيمة المقفلة
                    input.value = amountToClose.toFixed(2);
                    // خصم القيمة من رصيد العهدة المتبقي
                    remainingAdvance -= amountToClose;
                } else {
                    // إذا نفد رصيد العهدة، نترك القيمة فارغة (صفر)
                    input.value = ''; 
                }
            }
        });

        // ثالثاً: تحديث الإجمالي بعد التعبئة التلقائية
        updateTotalClosedAmount(); 
    }

    /**
     * تحديث رصيد العهدة وعرض قائمة الفواتير
     */
    function updateAdvanceAndInvoiceVisibility() {
        const selectedOption = advanceSelect.options[advanceSelect.selectedIndex];
        
        if (!advanceSelect.value) {
            invoicesSelectionSection.style.display = 'none';
            advanceInfo.classList.add("d-none");
            currentAdvanceRemaining = 0;
            // يتم التصفير عبر استدعاء autoFillAmounts بدون قيمة
            document.querySelectorAll('.invoice-closed-amount').forEach(input => input.value = '');
            updateTotalClosedAmount(); 
            return;
        }

        currentAdvanceRemaining = parseFloat(selectedOption.dataset.remaining || 0);
        currentAdvanceRemainingSpan.textContent = currentAdvanceRemaining.toLocaleString('en-US', { minimumFractionDigits: 2 });
        advanceInfo.classList.remove("d-none");
        invoicesSelectionSection.style.display = 'block';

        document.querySelectorAll('.invoice-closed-amount').forEach(input => input.disabled = false);

        // *** التعديل هنا: استدعاء دالة الملء التلقائي عند اختيار عهدة جديدة ***
        autoFillAmounts();
    }

    /**
     * حساب الإجمالي المقفل والتحقق من القيود
     * (تبقى كما هي لضمان عملها عند إدخال الموظف يدوياً)
     */
    function updateTotalClosedAmount() {
        const amountInputs = document.querySelectorAll('.invoice-closed-amount');
        let totalClosed = 0;
        let hasPositiveInput = false;

        amountInputs.forEach(input => {
            const row = input.closest('tr');
            const maxAvailable = parseFloat(row.dataset.availableAmount) || 0;
            let enteredAmount = parseFloat(input.value) || 0;

            // التحقق من تجاوز المبلغ المتاح في الفاتورة نفسها (تعديل قيمة الحقل)
            if (enteredAmount > maxAvailable) {
                enteredAmount = maxAvailable;
                input.value = enteredAmount.toFixed(2);
            }
            
            if (enteredAmount < 0) {
                 enteredAmount = 0;
                 input.value = ''; // نتركها فارغة بدلاً من صفر عند السلبية
            }
            
            if (enteredAmount > 0.001) {
                hasPositiveInput = true;
            }
            
            totalClosed += enteredAmount;
        });

        totalClosedInvoicesAmountSpan.textContent = totalClosed.toLocaleString('en-US', { minimumFractionDigits: 2 });
        totalClosedInvoicesAmountWarningSpan.textContent = totalClosed.toLocaleString('en-US', { minimumFractionDigits: 2 });
        
        let shouldDisable = false;

        // 1. التحقق من تجاوز رصيد العهدة
        if (totalClosed > currentAdvanceRemaining && currentAdvanceRemaining > 0) {
            advanceExceedWarning.classList.remove('d-none');
            shouldDisable = true;
        } else {
            advanceExceedWarning.classList.add('d-none');
        }
        
        // 2. التحقق من إدخال مبلغ تقفيل
        if (!hasPositiveInput && advanceSelect.value) {
            minAmountWarning.classList.remove('d-none');
            shouldDisable = true;
        } else {
             minAmountWarning.classList.add('d-none');
        }
        
        submitButton.disabled = shouldDisable || !advanceSelect.value;
    }
    
    /**
     * جمع بيانات الفواتير النشطة وإضافتها كمدخلات مخفية للنموذج قبل الإرسال
     * (تبقى كما هي)
     */
    function prepareFormSubmission(e) {
         // إذا كان الزر معطلاً لأي سبب، نمنع الإرسال
        if (submitButton.disabled) {
            e.preventDefault();
            return false;
        }

        manualClosureForm.querySelectorAll('input[name^="invoices"]').forEach(el => el.remove());

        const amountInputs = document.querySelectorAll('.invoice-closed-amount');
        let invoiceIndex = 0;

        amountInputs.forEach(input => {
            const closedAmount = parseFloat(input.value) || 0;
            
            if (closedAmount > 0.001) {
                const row = input.closest('tr');
                const invoiceId = row.dataset.invoiceId;
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `invoices[${invoiceIndex}][invoice_id]`;
                idInput.value = invoiceId;
                manualClosureForm.appendChild(idInput);

                const amountInput = document.createElement('input');
                amountInput.type = 'hidden';
                amountInput.name = `invoices[${invoiceIndex}][closed_amount]`;
                amountInput.value = closedAmount.toFixed(2); 
                manualClosureForm.appendChild(amountInput);
                
                invoiceIndex++;
            }
        });
        
        // تأكيد إيقاف الزر قبل الإرسال الفعلي
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جارٍ المعالجة...';
        
        return true;
    }


    // ربط الأحداث
    advanceSelect.addEventListener('change', updateAdvanceAndInvoiceVisibility);
    
    // ربط حدث الإدخال لجميع حقول المبلغ في الجدول
    document.querySelectorAll('.invoice-closed-amount').forEach(input => {
        input.addEventListener('input', updateTotalClosedAmount);
        // إضافة حدث عند ضياع التركيز لتثبيت القيمة العشرية
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value) || 0;
            // عرض القيمة العشرية إذا كانت موجبة، أو ترك الحقل فارغاً
            this.value = value > 0.001 ? value.toFixed(2) : ''; 
            updateTotalClosedAmount();
        });
    });

    // ربط حدث الإرسال لجمع البيانات الديناميكية
    manualClosureForm.addEventListener('submit', prepareFormSubmission);

    // التشغيل الأولي
    updateAdvanceAndInvoiceVisibility(); 
    

    // منطق زر التقفيل التلقائي (يبقى كما هو)
    const autoClosureButton = document.getElementById('auto-closure-button');
    const autoClosureForm = document.getElementById('auto-closure-form');
    
    if (autoClosureButton) {
        autoClosureButton.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من بدء عملية التقفيل التلقائي الشاملة؟ سيتم تقفيل العهد الأقدم أولاً باستخدام الفواتير المتاحة بنفس الترتيب.')) {
                e.preventDefault();
                return;
            }

            autoClosureButton.disabled = true;
            autoClosureButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جارٍ المعالجة...';

            setTimeout(() => {
                autoClosureForm.submit();
            }, 100);
        });
    }

</script>
@endpush