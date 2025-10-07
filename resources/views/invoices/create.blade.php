@extends('layouts.app_admin')
@section('title','تسجيل فاتورة جديدة')
@section('toolbar.title','لوحة التحكم')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-light py-3">
                <h3 class="mb-0 fw-bold text-dark">@yield('title')</h3>
                <small class="text-muted">
                    <a href="{{route('invoices.index')}}" class="fw-bold text-primary">جميع الفواتير</a>
                </small>
            </div>

            <form method="POST" action="{{ route('invoices.store') }}" enctype="multipart/form-data" class="p-4">
                @csrf
                @method('POST')

                <div class="row g-4">
                    <!-- نوع الفاتورة -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">نوع الفاتورة <span class="text-danger">*</span></label>
                        <select name="invoice_type" class="form-select select2">
                            <option hidden value="" selected>يرجي اختيار نوع الفاتورة</option>
                            <option value="Tax_Invoice" {{ old('invoice_type') === 'Tax_Invoice' ? 'selected' : '' }}>فاتورة ضريبية</option>
                            <option value="Invoice_with_Attachments" {{ old('invoice_type') === 'Invoice_with_Attachments' ? 'selected' : '' }}>فاتورة مع مرفقات</option>
                            <option value="Invoice_without_Attachments" {{ old('invoice_type') === 'Invoice_without_Attachments' ? 'selected' : '' }}>فاتورة بدون مرفقات</option>
                        </select>
                    </div>

                    <!-- المورد -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اختر المورد <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="supplier_id" id="supplier_id" class="form-select select2">
                                <option hidden value="">يرجي اختيار المورد</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name_ar }}</option>
                                @endforeach
                            </select>
                            @if(in_array(auth()->user()->role, ['cashier']))
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- الفروع -->
                <div class="mb-4">
                    <label class="form-label fw-bold">نوع الفاتورة بالنسبة للفروع <span class="text-danger">*</span></label>

                    <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="branch_type" id="branch_single" value="single" {{ old('branch_type')=='single' ? 'checked' : '' }}>
                            <label class="form-check-label" for="branch_single">فرع واحد</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="branch_type" id="branch_multiple" value="multiple" {{ old('branch_type')=='multiple' ? 'checked' : '' }}>
                            <label class="form-check-label" for="branch_multiple">عدة فروع</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="branch_type" id="branch_general" value="general" {{ old('branch_type')=='general' ? 'checked' : '' }}>
                            <label class="form-check-label" for="branch_general">فاتورة عامة</label>
                        </div>
                    </div>

                    <!-- اختيار الفروع -->
                    <div id="branch_select_wrapper" class="mt-3">
                        <label class="form-label">اختر الفروع</label>
                        <select name="branch_id[]" id="branch_id" class="form-select select2" multiple>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ in_array($branch->id, old('branch_id', [])) ? 'selected' : '' }}>
                                    {{ $branch->name_ar }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">اتركه فارغ إذا كانت الفاتورة عامة</small>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-4">
                    <!-- رقم الفاتورة -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">رقم الفاتورة</label>
                        <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" class="form-control" placeholder="رقم الفاتورة">
                    </div>

                    <!-- المبلغ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" value="{{ old('amount') }}" class="form-control" placeholder="المبلغ" required>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <!-- تاريخ الفاتورة -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ الفاتورة <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date') }}" class="form-control">
                    </div>

                    <!-- الوصف -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">وصف الفاتورة / سبب الصرف</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="أدخل تفاصيل إضافية">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-bold">ملف الفاتورة (PDF, JPG, PNG)</label>
                    <input type="file" name="file" class="form-control">
                </div>

                <div class="mt-5 d-flex justify-content-start gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> حفظ
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@include("modals.supplier")
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single,
    .select2-container .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 4px 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }
</style>
@endpush

@push('js')
<!-- Select2 JS -->

<script>
    // تهيئة select2
    $('.select2').select2({
        width: '100%',
        placeholder: "اختر",
        allowClear: true,
        dir: "rtl"
    });

    // إخفاء أو إظهار اختيار الفروع بناءً على branch_type
    // إخفاء أو إظهار واختيار الفروع حسب branch_type
    function toggleBranchSelect() {
        const type = $('input[name="branch_type"]:checked').val();

        if (type === 'general') {
            // إخفاء الفروع لو الفاتورة عامة
            $('#branch_select_wrapper').hide();
            $('#branch_id').prop('disabled', true).val(null).trigger('change');
        } 
        else if (type === 'single') {
            // عرض الفروع واختيار واحد فقط
            $('#branch_select_wrapper').show();
            $('#branch_id').prop('disabled', false)
                        .val(null).trigger('change')
                        .select2({ 
                            width: '100%', 
                            placeholder: "اختر فرع واحد", 
                            allowClear: true, 
                            dir: "rtl", 
                            maximumSelectionLength: 1 // 👈 يسمح بفرع واحد فقط
                            });
        } 
        else if (type === 'multiple') {
            // عرض الفروع واختيار أكثر من فرع
            $('#branch_select_wrapper').show();
            $('#branch_id').prop('disabled', false)
                        .val(null).trigger('change')
                        .select2({ 
                            width: '100%', 
                            placeholder: "اختر فروع متعددة", 
                            allowClear: true, 
                            dir: "rtl" 
                            });
        }
    }
    $('input[name="branch_type"]').on('change', toggleBranchSelect);
    toggleBranchSelect(); // تشغيل عند التحميل

    // إضافة مورد جديد
    $('#createSupplierForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res){
                $('#createSupplierModal').modal('hide');
                $('#createSupplierForm')[0].reset();
                let supplier = res.data;
                if(supplier.id && supplier.name_ar){
                    $('#supplier_id').append(
                        `<option value="${supplier.id}" selected>${supplier.name_ar}</option>`
                    ).trigger('change');
                }
                toastr.success('تم إضافة المورد بنجاح');
            },
            error: function(){
                toastr.error('فشل في إضافة المورد');
            }
        });
    });
</script>
@endpush
