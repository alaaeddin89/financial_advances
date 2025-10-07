@extends('layouts.app_admin')
@section('title','تعديل الفاتورة')
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
                        <a href="{{route('invoices.index')}}" class="fw-bolder link-primary">جميع الفواتير</a>.
                    </div>
                </div>

                <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- نوع الفاتورة والمورد --}}
                    <div class="form-group row mb-4">
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">نوع الفاتورة</span>
                            </label>
                            <select name="invoice_type" class="form-control">
                                <option hidden value="">يرجي اختيار نوع الفاتورة</option>
                                <option value="Tax_Invoice" {{ $invoice->invoice_type === 'Tax_Invoice' ? 'selected' : '' }}>فاتورة ضريبية</option>
                                <option value="Invoice_with_Attachments" {{ $invoice->invoice_type === 'Invoice_with_Attachments' ? 'selected' : '' }}>فاتورة مع مرفقات</option>
                                <option value="Invoice_without_Attachments" {{ $invoice->invoice_type === 'Invoice_without_Attachments' ? 'selected' : '' }}>فاتورة بدون مرفقات</option>
                            </select>
                        </div>

                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span>اختر المورد</span>
                            </label>
                            <select name="supplier_id" class="form-control">
                                <option hidden value="">يرجي اختيار المورد</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $invoice->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- نوع الفروع --}}
                    <div class="form-group row mb-4">
                        <div class="col-md-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الفروع المستفيدة من الفاتورة</span>
                            </label>

                            <div class="d-flex gap-4 mb-3">
                                <label><input type="radio" name="branch_type" value="single" {{ $invoice->is_general_expense ? '' : ($invoice->branches->count() === 1 ? 'checked' : '') }}> فرع واحد</label>
                                <label><input type="radio" name="branch_type" value="multiple" {{ $invoice->branches->count() > 1 ? 'checked' : '' }}> عدة فروع</label>
                                <label><input type="radio" name="branch_type" value="general" {{ $invoice->is_general_expense ? 'checked' : '' }}> فاتورة عامة</label>
                            </div>

                            <div id="branch_select_wrapper">
                                <select name="branch_id[]" id="branch_id" class="form-control" multiple data-placeholder="اختر فرع أو أكثر">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ in_array($branch->id, $invoice->branches->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $branch->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات الفاتورة --}}
                    <div class="form-group row mb-4">
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 mb-2">رقم الفاتورة</label>
                            <input type="text" class="form-control" name="invoice_no" value="{{ $invoice->invoice_no }}">
                        </div>

                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 mb-2">المبلغ</label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="{{ $invoice->amount }}" required>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">تاريخ الفاتورة</label>
                            <input type="date" class="form-control" name="invoice_date" 
                            value="{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}" />
                        </div>

                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 mb-2">وصف الفاتورة / سبب الصرف</label>
                            <textarea class="form-control" name="description" rows="3">{{ $invoice->description }}</textarea>
                        </div>
                    </div>

                    {{-- ملف الفاتورة --}}
                    <div class="form-group row mb-4">
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 mb-2">ملف الفاتورة (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" name="file">

                            @if($invoice->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->file_path))
                                <div class="mt-2">
                                    <a href="{{ route('invoices.download', $invoice->id) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-download"></i> تحميل المرفق
                                    </a>
                                    <a href="{{ asset('storage/'.$invoice->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> عرض المرفق
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- أزرار --}}
                    <div class="card-footer mt-5">
                        @if($invoice->status !== 'Rejected')
                        <button type="submit" class="btn btn-primary" id="updateBtn"><i class="fa fa-save"></i> تحديث</button>
                        @endif
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">إلغاء</a>

                        @if($invoice->status !== 'Rejected')
                            <button type="button" class="btn btn-danger" id="rejectBtn">
                                <i class="fa fa-times"></i> رفض الفاتورة
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// تفعيل إظهار/إخفاء الفروع حسب نوع branch_type
function toggleBranchSelect() {
    const type = $('input[name="branch_type"]:checked').val();
    if (type === 'general') {
        $('#branch_select_wrapper').hide();
        $('#branch_id').prop('disabled', true).val(null).trigger('change');
    } else if (type === 'single') {
        $('#branch_select_wrapper').show();
        $('#branch_id').prop('disabled', false).select2({
            width: '100%',
            placeholder: "اختر فرع واحد",
            allowClear: true,
            dir: "rtl",
            maximumSelectionLength: 1
        });
    } else if (type === 'multiple') {
        $('#branch_select_wrapper').show();
        $('#branch_id').prop('disabled', false).select2({
            width: '100%',
            placeholder: "اختر فروع متعددة",
            allowClear: true,
            dir: "rtl"
        });
    }
}

$('input[name="branch_type"]').on('change', toggleBranchSelect);
$(document).ready(toggleBranchSelect);

// رفض الفاتورة
document.getElementById('rejectBtn')?.addEventListener('click', function() {
    if(!confirm('هل أنت متأكد من رفض هذه الفاتورة؟')) return;
    $.ajax({
        url: '/invoices/{{ $invoice->id }}/reject',
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            alert(response.success);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseJSON.error || 'حدث خطأ');
        }
    });
});
</script>
@endpush
