{{-- resources/views/advances/show_details.blade.php --}}

@extends('layouts.app_admin') 
@section('title', 'تفاصيل العهدة رقم ' . $advance->id)
@section('toolbar.title', 'تفاصيل العهدة')

@section('content')
<div class="card shadow-sm mb-6">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title"><i class="bi bi-wallet2 me-2"></i> بيانات العهدة الرئيسية</h3>
    </div>
    <div class="card-body fs-6">
        <div class="row mb-3">
            <div class="col-md-4"><strong>رقم العهدة:</strong> <span class="badge bg-light text-dark">{{ $advance->id }}</span></div>
            <div class="col-md-4"><strong>الكاشير / المستفيد:</strong> {{ $advance->recipient->full_name ?? 'غير متوفر' }}</div>
            <div class="col-md-4"><strong>المبلغ الإجمالي:</strong> <span class="fw-bold text-primary">{{ number_format($advance->amount, 2) }}</span></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><strong>المبلغ المقفل:</strong> <span class="fw-bold text-success">{{ number_format($advance->closed_amount, 2) }}</span></div>
            <div class="col-md-4"><strong>المبلغ المتبقي:</strong> <span class="fw-bold text-danger">{{ number_format($advance->remaining_balance, 2) }}</span></div>
            <div class="col-md-4">
                <strong>الحالة:</strong> 
                <span class="badge 
                    @if($advance->status == 'مكتملة') bg-success 
                    @elseif($advance->status == 'معلقة') bg-warning 
                    @else bg-secondary @endif">
                    {{ $advance->status }}
                </span>
            </div>
        </div>

        {{-- Progress Bar --}}
        @php
            $percentage = $advance->amount > 0 ? ($advance->closed_amount / $advance->amount) * 100 : 0;
        @endphp
        <div class="mt-4">
            <strong>نسبة التقفيل:</strong>
            <div class="progress mt-2" style="height: 20px;">
                <div class="progress-bar 
                    @if($percentage == 100) bg-success 
                    @elseif($percentage >= 50) bg-info 
                    @else bg-warning @endif" 
                    role="progressbar" 
                    style="width: {{ $percentage }}%;" 
                    aria-valuenow="{{ $percentage }}" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                    {{ number_format($percentage, 0) }}%
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12"><strong>ملاحظات:</strong> 
                <p class="alert alert-light mt-2">{{ $advance->description ?? 'لا توجد ملاحظات.' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-6">
    <div class="card-header bg-info text-dark">
        <h3 class="card-title"><i class="bi bi-receipt-cutoff me-2"></i> الفواتير المغلقة بواسطة هذه العهدة ({{ $closures->count() }})</h3>
    </div>
    <div class="card-body">
        @if($closures->isEmpty())
            <div class="alert alert-info text-center"><i class="bi bi-info-circle me-2"></i> لم يتم تقفيل أي فواتير بهذه العهدة بعد.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center border rounded">
                    <thead class="bg-light">
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th>#</th>
                            <th>رقم الفاتورة</th>
                            <th>تاريخ الفاتورة</th>
                            <th>المبلغ المقفل</th>
                            <th>تاريخ التقفيل</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($closures as $index => $closure)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-primary">#{{ $closure->invoice->invoice_no ?? 'N/A' }}</span></td>
                                <td>{{ $closure->invoice->invoice_date ? \Carbon\Carbon::parse($closure->invoice->invoice_date)->format('Y-m-d') : 'N/A' }}</td>
                                <td class="fw-bold text-success">{{ number_format($closure->closed_amount, 2) }}</td>
                                <td>{{ $closure->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', $closure->invoice->id) }}" 
                                       class="btn btn-sm btn-light-primary">
                                        <i class="bi bi-eye"></i> عرض الفاتورة
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-start mt-4">
    <a href="{{ route('advances.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-right-circle"></i> العودة إلى كشف العهد
    </a>
</div>
@endsection
