@extends('layouts.app_admin')

@section('title','عرض العهدة')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('advances.index') }}">العهدات</a>
    </li>
    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="row g-3">

    {{-- رقم العهدة --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                <h6 class="text-muted">رقم العهدة</h6>
                <p class="fw-bold fs-5">#{{ $advance->id }}</p>
            </div>
        </div>
    </div>

    {{-- المستلم --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-user fa-2x text-success mb-2"></i>
                <h6 class="text-muted">المستلم</h6>
                <p class="fw-bold fs-5">{{ $advance->recipient->name ?? 'غير متوفر' }}</p>
            </div>
        </div>
    </div>

    {{-- المبلغ الإجمالي --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                <h6 class="text-muted">المبلغ الإجمالي</h6>
                <p class="fw-bold fs-5 text-success">{{ number_format($advance->amount,2) }}</p>
            </div>
        </div>
    </div>

    {{-- المبلغ المقفول --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-lock fa-2x text-info mb-2"></i>
                <h6 class="text-muted">المبلغ المقفول</h6>
                <p class="fw-bold fs-5 text-success">{{ number_format($advance->closed_amount,2) }}</p>
            </div>
        </div>
    </div>

    {{-- المبلغ المتبقي --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-wallet fa-2x text-danger mb-2"></i>
                <h6 class="text-muted">المبلغ المتبقي</h6>
                <p class="fw-bold fs-5 text-danger">{{ number_format($advance->amount - $advance->closed_amount,2) }}</p>
            </div>
        </div>
    </div>

    {{-- الحالة --}}
    <div class="col-12 col-md-2">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-info-circle fa-2x text-danger mb-2"></i>
                <h6 class="text-muted">الحالة</h6>
                <p class="fw-bold fs-5">
                    @switch($advance->status)
                        @case('Pending')
                            <span class="badge bg-warning text-dark">معلقة</span>
                            @break
                        @case('Confirmed')
                            <span class="badge bg-primary text-white">مؤكدة</span>
                            @break
                        @case('Partially Closed')
                            <span class="badge bg-info text-dark">مغلقة جزئيًا</span>
                            @break
                        @case('Closed')
                            <span class="badge bg-success">مغلقة</span>
                            @break
                        @default
                            <span class="badge bg-secondary">غير محددة</span>
                    @endswitch
                </p>
            </div>
        </div>
    </div>

    {{-- Progress Bar لنسبة التقفيل --}}
    @php
        $percentage = $advance->amount > 0 ? ($advance->closed_amount / $advance->amount) * 100 : 0;
    @endphp
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
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
        </div>
    </div>

    {{-- وصف العهدة مع Collapse --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span>وصف العهدة</span>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#descriptionCollapse" aria-expanded="true">
                    <i class="fas fa-angle-down"></i>
                </button>
            </div>
            <div id="descriptionCollapse" class="collapse show">
                <div class="card-body">
                    <p>{{ $advance->description ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- استبدال قسم الفواتير المغلقة --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light text-dark fw-bold d-flex justify-content-between align-items-center">
                <span>الفواتير المغلقة بواسطة هذه العهدة ({{ $closures->count() }})</span>
                <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#closuresCollapse" aria-expanded="true">
                    <i class="fas fa-angle-down"></i>
                </button>
            </div>
            <div id="closuresCollapse" class="collapse show">
                <div class="card-body">
                    @if($closures->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i> لم يتم تقفيل أي فواتير بهذه العهدة بعد.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle text-center">
                                <thead class="bg-light">
                                    <tr class="fw-bold text-gray-800">
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
                                                <a href="{{ route('invoices.show', $closure->invoice->id) }}" class="btn btn-sm btn-light-primary">
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
        </div>
    </div>


    <div class="col-12 text-end mt-3">
        <a href="{{ route('advances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> العودة للعهدات
        </a>
        {{-- زر قبول العهدة --}}
        @if($advance->status === 'Pending' && $advance->user_id === auth()->id())
            <form action="{{ route('advances.confirm', $advance->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من قبول هذه العهدة؟');">
                    <i class="fas fa-check me-1"></i> قبول العهدة
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
