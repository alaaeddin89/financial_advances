<script type="text/javascript">
    $(document).ready(function () {
        // --- 1. State Management Initialization (Use an Object/Map) ---
        // MUST be defined before the DataTable for global access
        globalThis.selectedRows = {}; 
        
        // --- 2. Initial Setup from URL Params ---
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('type_query') && urlParams.get('type_query') === 'advanciesNeedForAproved') {
            $('input[name="advanciesNeedForAproved"]').prop('checked', true);
        }
        
        // --- 3. DataTables Initialization ---
        globalThis.table = $('#table_id').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            info: true,
            paging: true,
            lengthChange: false,
            scrollX: true, // مهم لو جدولك عريض
            fixedColumns: { leftColumns: 1 },

            ajax: {
                url: "{{ route('advances.index') }}",
                type: 'GET',
                data: function(d) {
                    d.user_id = $("select[name='user_id']").val(); 
                    d.status = $("select[name='status']").val(); 
                    d.date_from = $("#date_from").val(); 
                    d.date_to = $("#date_to").val();
                    d.advanciesNeedForAproved = $('input[name="advanciesNeedForAproved"]').prop('checked') ? 1 : 0;
                },
                dataSrc: function (json) {
                    return json.data;
                }
            },

            dom: 'Bfrtip',  // ← لعرض الأزرار فوق الجدول
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm mx-1',
                    exportOptions: {
                        columns: ':visible:not(:last-child)',
                        modifier: {
                            search: 'applied',   // ✅ ياخد الفلاتر الحالية
                            order: 'applied'
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> طباعة',
                    className: 'btn btn-info btn-sm mx-1',
                    exportOptions: {
                        columns: ':visible:not(:last-child)',
                        modifier: {
                            search: 'applied',
                            order: 'applied'
                        }
                    },
                    customize: function (win) {
                        // جلب بيانات الفلاتر
                        let user = $("select[name='user_id'] option:selected").text() || 'الكل';
                        let status = $("select[name='status'] option:selected").text() || 'الكل';
                        let dateFrom = $("#date_from").val() || 'غير محدد';
                        let dateTo = $("#date_to").val() || 'غير محدد';

                        // بلوك الفلاتر
                        let filtersHtml = `
                            <div style="margin-bottom:20px; font-size:14px; text-align:right;">
                                <strong>الفلاتر المطبقة:</strong><br>
                                <span>الموظف: ${user}</span><br>
                                <span>الحالة: ${status}</span><br>
                                <span>من تاريخ: ${dateFrom}</span><br>
                                <span>إلى تاريخ: ${dateTo}</span>
                            </div>
                        `;

                        // إضافتها قبل الجدول
                        $(win.document.body).prepend(filtersHtml);

                        // تحسين اتجاه الجدول (عربي)
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
                {
                    data: 'id',
                    render: function(data) {
                        const isChecked = selectedRows[data] ? 'checked' : '';
                        return `<input type="checkbox" class="row-checkbox" value="${data}" ${isChecked}>`;
                    },
                    orderable: false,
                    searchable: false
                },
                {data: 'id', name: 'id'},
                {data: 'recipient.full_name', name: 'recipient.full_name'},
                {data: 'amount', name: 'amount'},
                {data: 'advance_status', name: 'advance_status'},
                {data: 'issue_date', name: 'issue_date'},
                {data: 'confirmation_date', name: 'confirmation_date'},
                {data: 'closed_amount', name: 'closed_amount'},
                {data: 'remaining_balance', name: 'remaining_balance'},
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
                $('#closed_amount').text(calc(7).toFixed(2));
                $('#remaining_balance').text(calc(8).toFixed(2));
            }
        });
        // End DataTables

        // --- 4. Event Handlers for Filtering (Simplified) ---
        // Note: The redundant table.page(currentPage).draw('page') calls were removed 
        // as table.ajax.reload() usually handles the draw completely.
        
        $('#submit').on('click', function () {
            table.ajax.reload();
        });

        $('select[name=status], select[name=user_id], input[type="checkbox"][name="advanciesNeedForAproved"]').on('change', function () {
             // Reload the table when any filter changes
            table.ajax.reload();
        });


        // --- 5. Select All Logic ---
        $('#select-all').on('change', function () {
            let isChecked = this.checked;
            // Iterate over checkboxes currently visible on the page
            $('.row-checkbox').each(function () { 
                let rowId = $(this).val();
                if (isChecked) {
                    selectedRows[rowId] = true;
                    $(this).prop('checked', true);
                } else {
                    delete selectedRows[rowId];
                    $(this).prop('checked', false);
                }
            });
        });

        // --- 6. Single Checkbox Logic ---
        // The delegate handler (on('change', '.row-checkbox')) is already well-written
        // and handles the selectedRows object correctly.

        // --- 7. Receive Advances AJAX Request ---
        $('#receive').on('click', function () {
            let ids = Object.keys(selectedRows);
            if (ids.length === 0) {
                alert("يرجى تحديد بعض العناصر التسليم.");
                return;
            }

            // CRITICAL FIX: Change this route to a dedicated route for receiving advances
            const receiveRoute = "{{ route('dashboard') }}"; // advances.receive

            $.ajax({
                url: receiveRoute, 
                method: 'post',
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message || "تم تحديث حالة العهد بنجاح.");
                    // Clear selected rows after successful operation
                    selectedRows = {};
                    $('#select-all').prop('checked', false);
                    // Reload the table to reflect new statuses
                    table.ajax.reload(); 
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.responseText);
                    alert("حدث خطأ أثناء التسليم. حاول مرة أخرى.");
                }
            });
        });

        // --- Optional: Fix for Initial Double Reload ---
        // The script already includes a reload at the end, but the filter change handlers 
        // ensure a reload happens. The extra reload here is likely redundant and slow.
        // You can comment out or remove these last three lines.
        // var currentPage = table.page();
        // table.ajax.reload(function() {
        //     table.page(currentPage).draw('page');
        // }); 

    });
</script>