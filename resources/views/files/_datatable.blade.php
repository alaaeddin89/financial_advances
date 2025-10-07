<script type="text/javascript">
    $(document).ready(function () {

        globalThis.table = $('#table_id').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('files.index') }}",
                type: 'GET',
                data: function(d) {
                    d.file_name = $("input[name='file_name']").val();
                    d.category_id = $("select[name='category_id']").val();
                    d.employee_name = $("input[name='employee_name']").val();
                    d.note = $("select[name='note']").val();
                    

                },
                dataSrc: function (json) {
                    return json.data;
                }
            },             language: {
                "lengthMenu": "عرض _MENU_ صف في الصفحة",
                "zeroRecords": "لم يتم إيجاد شيء",
                "info": "عرض صفحة _PAGE_ من _PAGES_",
                "infoEmpty": "لا يوجد أي بيانات متاحة",
                "infoFiltered": "(تصفية من _MAX_ العدد الكلي للصفوف)",
                "sSearch": "البحث:"

            },
            columns: [

                {data: 'id',name:'id'},
                {data: 'file_name', name: 'file_name'},
                {data: 'original_name', name: 'original_name'},
                {data: 'note', name: 'note'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]

        });
        $('#submit').on('click', function () {
            var currentPage = table.page();
            table.ajax.reload(function() {
                table.page(currentPage).draw('page');
            });

        });
    });
</script>
