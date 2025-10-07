@extends('layouts.app_admin')

@section('title','تفاصيل الفاتورة رقم #'.$invoice->id)
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- العنوان وزر التحميل --}}
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-dark mb-0">@yield('title')</h1>
            <div class="d-flex gap-2">
            @php
                $is_closed_in_advance = $invoice->closures()->where('accountant_approved', true)->exists();
            @endphp

            @if(!$is_closed_in_advance)
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-edit me-1"></i> تعديل الفاتورة
                </a>
            @endif
                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-warning shadow-sm" target="_blank">
                    <i class="fas fa-download me-1"></i> تحميل المرفق
                </a>
            </div>
        </div>
    </div>

    {{-- كروت ملخص سريعة (الصف الأول: معلومات أساسية) --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-user fa-2x text-primary mb-3"></i>
                <h6 class="text-muted">الموظف</h6>
                <h4 class="fw-bold text-dark">{{ $invoice->user->name }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-calendar-alt fa-2x text-info mb-3"></i>
                <h6 class="text-muted">تاريخ الإصدار</h6>
                <h4 class="fw-bold text-dark">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</h4>
            </div>
        </div>
    </div>
        {{-- كرت الفرع (الجديد) --}}
        <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-code-branch fa-2x text-warning mb-3"></i>
                <h6 class="text-muted">الفروع</h6>
                <h4 class="fw-bold text-dark" style="font-size: 1.1rem; line-height: 1.4;">{{ $branch_names }}</h4>
            </div>
        </div>
    </div>


    {{-- كروت ملخص سريعة (الصف الثاني: الوضع المالي) --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-money-bill-wave fa-2x text-success mb-3"></i>
                <h6 class="text-muted">المبلغ الإجمالي</h6>
                <h3 class="fw-bold text-success">{{ number_format($invoice->amount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-flag fa-2x 
                    @if($invoice->status == 'Approved') text-success 
                    @elseif($invoice->status == 'Rejected') text-danger 
                    @else text-warning @endif mb-3"></i>
                <h6 class="text-muted">الحالة</h6>
                <h4 class="fw-bold">
                    <span class="badge 
                        @if($invoice->status == 'Approved') bg-success 
                        @elseif($invoice->status == 'Rejected') bg-danger 
                        @else bg-warning text-dark @endif">
                        {{ $invoice->status }}
                    </span>
                </h4>
            </div>
        </div>
    </div>

    {{-- الوصف --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-align-right"></i> الوصف</h5>
                <p class="alert alert-light fs-6 mb-0">{{ $invoice->description ?? 'لا يوجد وصف.' }}</p>
            </div>
        </div>
    </div>

    {{-- بيانات التقفيل --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-wallet fa-2x text-primary mb-3"></i>
                <h6 class="text-muted">المبلغ المستخدم للتقفيل</h6>
                <h3 class="fw-bold text-primary">{{ number_format($invoice->used_amount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <i class="fas fa-coins fa-2x text-success mb-3"></i>
                <h6 class="text-muted">المبلغ المتاح للتقفيل</h6>
                <h3 class="fw-bold text-success">{{ number_format($invoice->amount - $invoice->used_amount, 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- زر العودة --}}
    <div class="col-12 text-end">
        <a href="{{ url()->previous() }}" class="btn btn-light shadow-sm">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>
</div>
@endsection
