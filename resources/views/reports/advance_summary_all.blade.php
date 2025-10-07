@extends('layouts.app_admin')
@section('title','تقرير ملخص العهد لجميع الموظفين')
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
                        حدد فترة زمنية لعرض ملخص العهد لجميع الموظفين.
                    </div>
                </div>

                {{-- فورم البحث (بدون اختيار موظف) --}}
                <form id="advanceSummaryForm" class="mb-5">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="date_from" class="form-label fw-bold">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" required>
                        </div>

                        <div class="col-md-5">
                            <label for="date_to" class="form-label fw-bold">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" required>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> عرض الملخص
                            </button>
                        </div>
                    </div>
                </form>

                {{-- نتيجة التقرير --}}
                <div class="report-card card p-4" id="reportResult" style="display: none;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">
                            ملخص العهد لجميع الموظفين
                        </h4>
                        
                        {{-- زر الطباعة/التصدير --}}
                        <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                            <i class="fa fa-print"></i> طباعة / تصدير PDF
                        </button>
                    </div>

                    {{-- محتوى التقرير القابل للطباعة --}}
                    <div id="printableReportContent"> 
                        <p class="text-muted text-center mt-3">
                            الفترة الزمنية المحددة: <span id="periodDisplay"></span>
                        </p>

                        {{-- إجمالي التقرير العام --}}
                        <div class="row text-center mb-5">
                            <h5 class="mb-3">الإجمالي العام للفترة</h5>
                            <div class="col-md-4 mb-3">
                                <div class="alert alert-success shadow-sm rounded">
                                    <h6 class="fw-bold">إجمالي المسلّم 💰</h6>
                                    <p class="h3 mb-0" id="grandTotalIssued">0.00</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="alert alert-warning shadow-sm rounded">
                                    <h6 class="fw-bold">إجمالي المصروف 🧾</h6>
                                    <p class="h3 mb-0" id="grandTotalClosed">0.00</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="alert alert-danger shadow-sm rounded">
                                    <h6 class="fw-bold">الرصيد المتبقي الصافي 🚨</h6>
                                    <p class="h3 mb-0" id="grandRemainingBalance">0.00</p>
                                </div>
                            </div>
                        </div>


                        {{-- جدول ملخص الموظفين --}}
                        <h5 class="mt-5 mb-3">تفاصيل العهد لكل موظف</h5>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle text-center" id="summaryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>الموظف</th>
                                        <th>المبلغ المسلّم</th>
                                        <th>المبلغ المصروف (المقفل)</th>
                                        <th>الرصيد المتبقي (الصافي)</th>
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
document.getElementById('advanceSummaryForm').addEventListener('submit', function(e){
    e.preventDefault();

    let date_from = document.getElementById('date_from').value;
    let date_to   = document.getElementById('date_to').value;

    document.getElementById('reportResult').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';

    // مسار الـ API الجديد للتقرير الشامل
    axios.post("{{ route('reports.advance.all.data') }}", {
        date_from, date_to, _token: '{{ csrf_token() }}'
    })
    .then(res => {
        let totals = res.data.grand_totals;
        let reports = res.data.reports || [];

        document.getElementById('reportResult').style.display = 'block';
        
        // 1. تحديث الإجمالي العام
        document.getElementById('grandTotalIssued').textContent    = totals.total_issued.toFixed(2);
        document.getElementById('grandTotalClosed').textContent    = totals.total_closed.toFixed(2);
        document.getElementById('grandRemainingBalance').textContent = totals.net_balance.toFixed(2);
        document.getElementById('periodDisplay').textContent    = `${date_from} إلى ${date_to}`;

        // 2. تعبئة جدول ملخص الموظفين
        let tbody = document.querySelector('#summaryTable tbody');
        tbody.innerHTML = '';

        if (reports.length > 0) {
            reports.forEach(report => {
                let row = `
                    <tr>
                        <td>${report.employee_name}</td>
                        <td class="text-success fw-bold">${report.total_issued.toFixed(2)}</td>
                        <td class="text-warning fw-bold">${report.total_closed.toFixed(2)}</td>
                        <td class="text-danger fw-bold">${report.net_balance.toFixed(2)}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-muted">لا توجد حركات عهد للموظفين ضمن الفترة المحددة</td></tr>`;
        }
    })
    .catch(err => {
        document.getElementById('reportResult').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('errorMessage').innerText = err.response?.data?.message || 'حدث خطأ أثناء جلب البيانات.';
    });
});

/**
 * دالة طباعة محتوى التقرير فقط (للتصدير كـ PDF عبر المتصفح)
 */
function printReport() {
    const reportContent = document.getElementById('printableReportContent');
    const reportTitle = document.querySelector('h1').textContent;
    
    if (!reportContent) {
        alert('لا يوجد محتوى للتقرير للعرض.');
        return;
    }
    
    const originalBody = document.body.innerHTML;
    const contentToPrint = reportContent.cloneNode(true); 
    
    // إضافة العنوان الرئيسي للتقرير
    const headerHtml = `
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin-bottom: 5px;">${reportTitle}</h3>
        </div>
    `;
    
    // إعداد محتوى الصفحة للطباعة
    document.body.innerHTML = `
        <html>
            <head>
                <title>${reportTitle}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
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
    window.location.reload(); 
}
</script>
@endpush