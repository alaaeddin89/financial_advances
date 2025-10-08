@extends('layouts.app_admin')
@section('title','كشف رصيد العهد للموظف')
@section('toolbar.title','التقارير')
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
            <div class="card-body">
                <div class="mb-10 text-start">
                    <h1 class="mb-3">@yield('title')</h1>
                    <div class="text-gray-500 fw-bold fs-6">
                        اختر موظفاً وحدد فترة زمنية لعرض كشف الرصيد.
                    </div>
                </div>

                {{-- فورم البحث --}}
                <form id="advanceReportForm" class="mb-5">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="user_id" class="form-label fw-bold">اختر الموظف</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">-- اختر موظفاً --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="date_from" class="form-label fw-bold">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" required>
                        </div>

                        <div class="col-md-3">
                            <label for="date_to" class="form-label fw-bold">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" required>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> عرض الكشف
                            </button>
                        </div>
                    </div>
                </form>

                {{-- نتيجة التقرير --}}
                <div class="report-card card p-4" id="reportResult" style="display: none;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title" id="reportTitle">
                            تقرير العهد للموظف: <span></span>
                        </h4>
                        
                        {{-- زر الطباعة/التصدير --}}
                        <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                            <i class="fa fa-print"></i> طباعة / تصدير PDF
                        </button>
                    </div>

                    {{-- محتوى التقرير القابل للطباعة --}}
                    <div id="printableReportContent"> 
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="alert alert-success shadow-sm rounded">
                                    <h6 class="fw-bold">المبلغ المسلّم 💰</h6>
                                    <p class="h3 mb-0" id="totalIssued">0.00</p>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="alert alert-warning shadow-sm rounded">
                                    <h6 class="fw-bold">المبلغ المصروف (المقفل) 🧾</h6>
                                    <p class="h3 mb-0" id="totalClosed">0.00</p>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="alert alert-danger shadow-sm rounded">
                                    <h6 class="fw-bold">الرصيد المتبقي (الصافي) 🚨</h6>
                                    <p class="h3 mb-0" id="remainingBalance">0.00</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted text-center mt-3">
                            الفترة الزمنية المحددة: <span id="periodDisplay"></span>
                        </p>

                        {{-- جدول العمليات --}}
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle text-center" id="transactionsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>البيان</th>
                                        <th>المبلغ</th>
                                        <th>النوع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- سيتم تعبئة البيانات عبر جافاسكريبت --}}
                                </tbody>
                            </table>
                        </div>
                    </div> {{-- نهاية محتوى الطباعة --}}
                </div>

                {{-- رسالة خطأ --}}
                <div class="alert alert-danger mt-3" id="errorMessage" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('advanceReportForm').addEventListener('submit', function(e){
    e.preventDefault();

    let user_id   = document.getElementById('user_id').value;
    let date_from = document.getElementById('date_from').value;
    let date_to   = document.getElementById('date_to').value;

    // إخفاء النتائج السابقة ورسائل الخطأ
    document.getElementById('reportResult').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
    
    // التحقق من المدخلات
    if (!user_id || !date_from || !date_to) {
        document.getElementById('errorMessage').innerText = 'يرجى اختيار الموظف وتحديد الفترة الزمنية كاملة.';
        document.getElementById('errorMessage').style.display = 'block';
        return;
    }

    // الطلب إلى مسار API (reports.advance.balance يجب أن يكون معرّفاً في routes/api.php)
    axios.post("{{ route('reports.advance.employee.data') }}", {
        user_id, date_from, date_to
    })
    .then(res => {
        let reportData = res.data.report;
        let transactions = res.data.transactions || [];

        // 1. تحديث الملخص
        document.getElementById('reportResult').style.display = 'block';
        document.querySelector('#reportTitle span').textContent = reportData.employee_name;
        document.getElementById('totalIssued').textContent    = parseFloat(reportData.total_issued).toFixed(2);
        document.getElementById('totalClosed').textContent    = parseFloat(reportData.total_closed).toFixed(2);
        document.getElementById('remainingBalance').textContent = parseFloat(reportData.net_balance).toFixed(2);
        document.getElementById('periodDisplay').textContent    = `${date_from} إلى ${date_to}`;

        // 2. تعبئة جدول العمليات
        let tbody = document.querySelector('#transactionsTable tbody');
        tbody.innerHTML = '';

        if (transactions.length > 0) {
            transactions.forEach(trx => {
                let amountDisplay = parseFloat(trx.amount).toFixed(2);
                let badgeHtml = trx.type === 'issued' 
                    ? '<span class="badge bg-success">مسلم</span>' 
                    : '<span class="badge bg-warning text-dark">مصروف</span>';

                let row = `
                    <tr>
                        <td>${trx.date}</td>
                        <td>${trx.description || '-'}</td>
                        <td>${amountDisplay}</td>
                        <td>${badgeHtml}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-muted">لا توجد عمليات ضمن الفترة المحددة</td></tr>`;
        }
    })
    .catch(err => {
        document.getElementById('reportResult').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'block';
        
        let message = 'حدث خطأ أثناء جلب البيانات.';
        if (err.response) {
            // التعامل مع أخطاء التحقق (422) أو غيرها
            message = err.response.data.message || err.response.data.errors?.user_id?.[0] || message;
        }
        document.getElementById('errorMessage').innerText = message;
    });
});

/**
 * دالة طباعة محتوى التقرير فقط (للتصدير كـ PDF عبر المتصفح)
 */
function printReport() {
    const reportContent = document.getElementById('printableReportContent');
    const fullReportTitle = document.getElementById('reportTitle').textContent;
    
    if (!reportContent) {
        alert('لا يوجد محتوى للتقرير للعرض.');
        return;
    }
    
    const originalBody = document.body.innerHTML;
    const contentToPrint = reportContent.cloneNode(true); 
    
    // إضافة العنوان الرئيسي للتقرير
    const headerHtml = `
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin-bottom: 5px;">${fullReportTitle}</h3>
        </div>
    `;
    
    // إعداد محتوى الصفحة للطباعة
    document.body.innerHTML = `
        <html>
            <head>
                <title>كشف رصيد العهد - ${document.querySelector('#reportTitle span').textContent}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    /* تحسين مظهر الطباعة */
                    .shadow-sm { box-shadow: none !important; }
                    .alert { border: 1px solid #ccc !important; padding: 10px; }
                    .table { margin-top: 15px; }
                </style>
            </head>
            <body dir="rtl" style="padding: 20px;">
                ${headerHtml}
                ${contentToPrint.outerHTML}
            </body>
        </html>
    `;
    
    // تنفيذ أمر الطباعة
    window.print();
    
    // استعادة محتوى الـ body الأصلي
    document.body.innerHTML = originalBody;
    // إعادة تحميل لضمان استعادة كافة أحداث JavaScript
    window.location.reload(); 
}


// تحديد القيم الافتراضية لحقلَي التاريخ
document.addEventListener("DOMContentLoaded", function () {
    // الحصول على التاريخ الحالي
    const today = new Date();

    // إنشاء تاريخ لأول يوم في الشهر الحالي
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    // تنسيق التاريخ بصيغة YYYY-MM-DD
    const formatDate = (date) => {
        const year  = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day   = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // تعبئة القيم في الحقول
    document.getElementById('date_from').value = formatDate(firstDay);
    document.getElementById('date_to').value   = formatDate(today);
});
</script>
@endpush