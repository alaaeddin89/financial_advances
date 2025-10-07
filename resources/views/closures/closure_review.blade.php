@extends('layouts.app_admin')

@section('title','مراجعة واعتماد عمليات التقفيل')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body py-4">
                <div class="mb-4">
                    <h1 class="mb-2 fw-bold">@yield('title')</h1>
                    <p class="text-muted fs-6">
                        عمليات التقفيل التالية تمت من قبل الموظفين وتنتظر اعتمادك النهائي.
                    </p>
                </div>

                {{-- رسائل النظام --}}
       

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($pendingClosures->isEmpty())
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        لا توجد عمليات تقفيل معلقة بانتظار الاعتماد النهائي حالياً.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr class="fw-bold text-muted">
                                    <th>#</th>
                                    <th class="d-none d-md-table-cell"><i class="fas fa-user"></i> الموظف</th>
                                    <th class="d-none d-md-table-cell"><i class="fas fa-calendar-alt"></i> التاريخ</th>
                                    <th><i class="fas fa-money-bill-wave"></i> مبلغ التقفيل</th>
                                    <th>العهدة</th>
                                    <th class="d-none d-md-table-cell">الفاتورة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingClosures as $closure)
                                <tr>
                                    <td class="fw-bold">{{ $closure->id }}</td>
                                    <td class="d-none d-md-table-cell">{{ $closure->advance->recipient->name ?? 'غير متوفر' }}</td>
                                    <td class="d-none d-md-table-cell">{{ $closure->closure_date->format('Y-m-d H:i') }}</td>
                                    <td class="fw-bold text-success">{{ number_format($closure->closed_amount, 2) }}</td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-primary mb-1">عهدة #{{ $closure->advance_id }}</span>
                                            <small class="text-muted mb-1 d-none d-md-block">{{ Str::limit($closure->advance->description, 30) }}</small>
                                            <a href="{{ route('advances.show', $closure->advance_id) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               target="_blank" 
                                               data-bs-toggle="tooltip" 
                                               title="عرض العهدة">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-info text-dark mb-1">فاتورة #{{ $closure->invoice_id }}</span>
                                            <small class="text-muted mb-1">المبلغ: {{ number_format($closure->invoice->amount, 2) }}</small>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('invoices.show', $closure->invoice_id) }}" 
                                                   class="btn btn-outline-secondary btn-sm" 
                                                   target="_blank" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                                <a href="{{ route('invoices.download', $closure->invoice_id) }}" 
                                                   class="btn btn-warning btn-sm" target="_blank" 
                                                   data-bs-toggle="tooltip" title="تحميل المرفق">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 justify-content-center">
                                            <form action="{{ route('closures.approve', $closure->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="اعتماد نهائي"
                                                        onclick="return confirm('هل أنت متأكد من اعتماد عملية التقفيل هذه؟')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <div class="wdith:100%">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal"
                                                    data-closure-id="{{ $closure->id }}"
                                                    data-base-url="{{ url('closures/reject') }}"
                                                    title="رفض العملية">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal إدخال سبب الرفض --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">رفض عملية التقفيل</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- النموذج الذي سيرسل البيانات إلى دالة rejectClosure --}}
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-danger">لرفض عملية التقفيل، يرجى إدخال سبب واضح ومفصل:</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label required">سبب الرفض (الحد الأدنى 10 أحرف)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required minlength="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var rejectModal = document.getElementById('rejectModal');

    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var base = button.getAttribute('data-base-url');
            var closureId = button.getAttribute('data-closure-id');

            var form = document.getElementById('rejectForm');

            if (!base || !closureId) {
                console.error('rejectModal: missing base or closureId', base, closureId);
                form.action = ''; // امنع الإرسال لحين تصحيح المشكلة
                return;
            }

            // تأكد من أن المسار لا ينتهي بشرطة مائلة مضاعفة
            form.action = base.replace(/\/$/, '') + '/' + closureId;

            // تسهيل تصحيح الأخطاء — يمكنك إزالة الـ console بعد التأكد
            console.log('Reject form action set to:', form.action);
        });

        // منع الإرسال إذا لم يتم تعيين action (fallback أمني)
        var rejectForm = document.getElementById('rejectForm');
        rejectForm.addEventListener('submit', function(e) {
            if (!this.action || this.action.trim() === '') {
                e.preventDefault();
                alert('خطأ: مسار الرفض غير محدد. تأكد من تشغيل الجافاسكربت على الصفحة وإعادة تحميلها.');
            }
        });
    }
});
</script>

@endpush

