@extends('layouts.app_admin')
@section('title','كشف المصروفات/التقفيل المعتمد')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">التقارير</li>
@endsection
@push('css')
    <link href="{{asset('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    {{-- إضافة CSS لأزرار التصدير --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    
    <style>
        th, td { text-align: center; }
        /* ... باقي CSS الخاص بك ... */
    </style>
@endpush
@section('content')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="text-align: right;">

                <div class="card-body py-3">
                    <div class="mb-13 mt-5 text-start">
                        <h1 class="mb-3">@yield('title')</h1>
                    </div>
                    
                    {{-- لا نحتاج زر إضافة عهدة هنا --}}
                    
                    <div class="form-group row">
                        
                        <div class="col-md-3 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الكاشير (مستلم العهدة)</span>
                            </label>
                            <select name="user_id" id="user_id" class="form-control" data-control="select2">
                                <option hidden value="" selected>يرجى الاختيار (الكل)</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">المورد</span>
                            </label>
                            <select name="supplier_id" id="supplier_id" class="form-control" data-control="select2">
                                <option hidden value="" selected>يرجى الاختيار (الكل)</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">من تاريخ تقفيل</span>
                            </label>
                            <input type="date" name="date_from" id="date_from" class="form-control" />
                        </div>
                        
                        <div class="col-md-2 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">إلى تاريخ تقفيل</span>
                            </label>
                            <input type="date" name="date_to" id="date_to" class="form-control" />
                        </div>
                        
                        <div class="col-lg-2 fv-row d-flex flex-column justify-content-end">
                            <button type="button" id="submit_filter" class="btn btn-primary">
                                <span class="indicator-label"><i class="fa fa-search"></i> بحث </span>
                            </button>

                            <button type="button" id="print_report_btn" class="btn btn-light-info">
                                <span class="indicator-label"><i class="fa fa-print"></i> طباعة التقرير </span>
                            </button>

                            <button type="button" id="download_attachments_btn" class="btn btn-success mt-2">
                                <span class="indicator-label"><i class="fa fa-download"></i> تحميل مرفقات الفواتير</span>
                            </button>
                        </div>

                        
                    </div>

                    
                    <div class="table-responsive" >
                        <table id="closure_report_table"
                                class="table table-bordered table-hover table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center fs-7">
                            <thead>
                            <tr class="fw-bolder bg-secondary text-muted ">
                                <th class="max-w-40px text-center">#</th>
                                <th class="min-w-60px text-center">رقم العهدة</th>
                                <th class="min-w-150px text-center">وصف العهدة</th> 
                                <th class="min-w-100px text-center">الكاشير (المستلم)</th>
                                
                                <th class="min-w-80px text-center">رقم الفاتورة</th> 
                                <th class="min-w-100px text-center">تاريخ الفاتورة</th> 
                                <th class="min-w-200px text-center">تفاصيل البند (الفاتورة)</th>
                                <th class="min-w-100px text-center">المبلغ المعتمد</th>
                                <th class="min-w-100px text-center">اسم المورد</th>
                                
                                <th class="min-w-100px text-center">تاريخ التقفيل</th>
                                <th class="min-w-150px text-center">المحاسب المعتمد</th> 
                                <th class="min-w-60px text-center">إجراءات</th> </tr>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">المجموع الكلي للصفحة:</th>
                                    <th id="total_amount_footer" class="text-center bg-light-danger"></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    {{-- إضافات DataTables Buttons (للتصدير والطباعة) --}}
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            
            // 1. DataTables Initialization
            var reportTable = $('#closure_report_table').DataTable({
                processing: true,
                serverSide: true,
                "searching": false, 
                "info": true, 
                "paging": true, 
                "lengthChange": false, 
                order: [[ 9, "desc" ]], // الترتيب حسب تاريخ التقفيل
                
                //  إعدادات أزرار التصدير والطباعة 
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> تصدير إلى إكسل',
                        className: 'd-none', // 💡 إخفاء الزر افتراضياً
                        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] } 
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> طباعة التقرير',
                        className: 'd-none', // 💡 إخفاء الزر افتراضياً
                        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                        customize: function ( win ) {
                            $(win.document.body).css('direction', 'rtl').css('font-family', 'Arial, sans-serif');
                            $(win.document.body).find('table').css('direction', 'rtl').addClass('rtl-table');
                        }
                    }
                ],
                //  نهاية إعدادات الأزرار 
                
                ajax: {
                    url: "{{ route('advances.closure_report') }}", // تأكد من أن هذا الـ Route صحيح
                    type: 'GET',
                    data: function(d) {
                        d.user_id = $("#user_id").val(); 
                        d.date_from = $("#date_from").val(); 
                        d.date_to = $("#date_to").val();
                        d.supplier_id = $('#supplier_id').val();
                    },
                },
                language: {
                    "lengthMenu": "عرض _MENU_ صف",
                    "zeroRecords": "لم يتم إيجاد شيء",
                    "info": "عرض صفحة _PAGE_ من _PAGES_",
                    "infoEmpty": "لا يوجد أي بيانات متاحة",
                    "infoFiltered": "(تصفية من _MAX_ العدد الكلي للصفوف)",
                    "sSearch": "البحث:",
                    "paginate": { "next": "التالي", "previous": "السابق" }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, // 0
                    {data: 'advance_id_num', name: 'advance_id'}, // 1
                    {data: 'advance_description', name: 'advance.description', orderable: false, searchable: false}, // 2
                    {data: 'cashier_name', name: 'advance.recipient.full_name'}, // 3

                    {data: 'invoice_number', name: 'invoice.invoice_no'}, // 4
                    {data: 'invoice_date', name: 'invoice.invoice_date'}, // 5
                    {data: 'item_details', name: 'invoice.description', orderable: false}, // 6
                    {data: 'invoice_amount', name: 'closed_amount'}, // 7
                    {data: 'supplier_name', name: 'invoice.supplier.name', orderable: false, searchable: false}, // 8
                    
                    {data: 'closure_date', name: 'closure_date'}, // 9
                    {data: 'approver_name', name: 'closer.full_name', orderable: false, searchable: false}, // 10
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],

                // 2. Footer Callback for Totals
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    
                    // تحويل المبلغ إلى رقم قابل للجمع
                    var total = api
                        .column(7, { page: 'current' }) // العمود رقم 4 هو 'المبلغ المعتمد'
                        .data()
                        .reduce(function(a, b) {
                            // إزالة الفواصل إن وجدت قبل التحويل
                            var numA = parseFloat(String(a).replace(/,/g, '') || 0); 
                            var numB = parseFloat(String(b).replace(/,/g, '') || 0);
                            return numA + numB;
                        }, 0);
                    
                    // تحديث المجموع في التذييل
                    $('#total_amount_footer').html(number_format(total, 2));
                }
            });

            // دالة لتنسيق الأرقام
            function number_format(number, decimals, dec_point, thousands_sep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + Math.round(n * k) / k;
                    };
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }

            // 3. Event Handler for Filtering
            $('#submit_filter').on('click', function () {
                reportTable.ajax.reload();
            });

            // باقي حقول الفلترة لا تعيد تحميل الجدول تلقائياً
            $('#user_id, #date_from, #date_to, #supplier_id').on('change', function () {
                // لا يوجد كود هنا، ننتظر ضغطة "بحث"
            });

            $('#print_report_btn').on('click', function () {
                // 1. إعادة تحميل البيانات مع الفلاتر الحالية
                // عند نجاح التحميل، يتم تنفيذ دالة callback
                reportTable.ajax.reload(function() {
                    // 2. تشغيل زر الطباعة المخفي (الفهرس [1] هو زر الطباعة)
                    reportTable.button(1).trigger(); 
                }, false); // 'false' للحفاظ على رقم الصفحة الحالية
            });

        });

        $('#download_attachments_btn').on('click', function () {
            // نمرر الفلاتر الحالية للـ Route
            let params = {
                user_id: $("#user_id").val(),
                supplier_id: $("#supplier_id").val(),
                date_from: $("#date_from").val(),
                date_to: $("#date_to").val()
            };
            let query = $.param(params);
            window.location.href = "{{ route('advances.closure_report.download_attachments') }}?" + query;
        });
    </script>
@endpush