<script type="text/javascript">
    $(document).ready(function () {
        let urlParams = new URLSearchParams(window.location.search);
        
        
        globalThis.table = $('#table_id').DataTable({
            processing: true,
            serverSide: true,
            stateSave:true,
            searching: false,
            paging: true,
            lengthChange: false,
            scrollX: true,
            fixedColumns: { leftColumns: 1 },

            ajax: {
                url: "{{ route('invoices.index') }}",
                type: 'GET',
                data: function(d) {
                    d.user_id = $("select[name='user_id']").val(); 
                    d.date_from = $("#date_from").val(); 
                    d.date_to = $("#date_to").val();
                    d.closure_status = $("select[name='closure_status']").val();
                    
                },
                dataSrc: function (json) {
                    return json.data;
                }
            },

            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> تصدير Excel',
                    className: 'btn btn-success btn-sm mx-1',
                    exportOptions: {
                        columns: ':visible:not(:last-child)',
                        modifier: { search: 'applied', order: 'applied' }
                    },
                    customize: function (xlsx) {
                        let user = $("select[name='user_id'] option:selected").text() || 'الكل';
                        let dateFrom = $("#date_from").val() || 'غير محدد';
                        let dateTo = $("#date_to").val() || 'غير محدد';
                        let sheet = xlsx.xl.worksheets['sheet1.xml'];
                        let filtersText = `الفلاتر: الموظف=${user} | من=${dateFrom} | إلى=${dateTo}`;

                        let row = sheet.getElementsByTagName('row')[0];
                        let newNode = sheet.createElement('row');
                        newNode.setAttribute("r", "1");
                        let cell = sheet.createElement('c');
                        cell.setAttribute("t", "inlineStr");
                        cell.setAttribute("r", "A1");
                        let isNode = sheet.createElement('is');
                        let tNode = sheet.createElement('t');
                        tNode.textContent = filtersText;
                        isNode.appendChild(tNode);
                        cell.appendChild(isNode);
                        newNode.appendChild(cell);
                        sheet.insertBefore(newNode, row);
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> طباعة',
                    className: 'btn btn-info btn-sm mx-1',
                    exportOptions: {
                        columns: ':visible:not(:last-child)',
                        modifier: { search: 'applied', order: 'applied' }
                    },
                    customize: function (win) {
                        let user = $("select[name='user_id'] option:selected").text() || 'الكل';
                        let dateFrom = $("#date_from").val() || 'غير محدد';
                        let dateTo = $("#date_to").val() || 'غير محدد';

                        let filtersHtml = `
                            <div style="margin-bottom:20px; font-size:14px; text-align:right;">
                                <strong>الفلاتر المطبقة:</strong><br>
                                <span>الموظف: ${user}</span><br>
                                <span>من تاريخ: ${dateFrom}</span><br>
                                <span>إلى تاريخ: ${dateTo}</span>
                            </div>
                        `;

                        $(win.document.body).prepend(filtersHtml);
                        $(win.document.body).css('direction', 'rtl');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', '12px');
                    }
                }
            ],

            language: {
                "lengthMenu": "عرض _MENU_ صف في الصفحة",
                "zeroRecords": "لم يتم إيجاد شيء",
                "info": "عرض صفحة _PAGE_ من _PAGES_",
                "infoEmpty": "لا يوجد أي بيانات متاحة",
                "infoFiltered": "(تصفية من _MAX_ العدد الكلي للصفوف)",
                "sSearch": "البحث:",
                "paginate": {
                    "next": "التالي",
                    "previous": "السابق"
                }
            },

            columns: [
                {data: 'id', name: 'id'},
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'supplier.name_ar', name: 'supplier.name_ar'},
                {data: 'amount', name: 'amount'},
                {data: 'invoice_status', name: 'invoice_status'},
                {data: 'invoice_date', name: 'invoice_date'},
                {data: 'used_amount', name: 'used_amount'},
                {data: 'closure_status', name: 'closure_status', orderable: false, searchable: false}, // ← العمود الجديد
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],

            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var calc = function(idx) {
                    return api.column(idx, {page: 'current'}).data().reduce(function(a, b) {
                        return parseFloat(a || 0) + parseFloat(b || 0);
                    }, 0);
                };
                $('#amount').text(calc(3).toFixed(2));
                $('#closed_amount').text(calc(6).toFixed(2));
                
            }
        });

       

       

        $('#submit').on('click', function () {
            var currentPage = table.page();
            table.ajax.reload(function() {
                table.page(currentPage).draw('page');
            });

        });

 

        $('select[name=user_id]').change(function () {
            var currentPage = table.page();
            table.ajax.reload(function() {
                table.page(currentPage).draw('page');
            });
        });

        $('#closure_status').change(function () {
            var currentPage = table.page();
            table.ajax.reload(function() {
                table.page(currentPage).draw('page');
            });
        });

       


        var currentPage = table.page();
        table.ajax.reload(function() {
            table.page(currentPage).draw('page');
        });
        

    });

</script>
