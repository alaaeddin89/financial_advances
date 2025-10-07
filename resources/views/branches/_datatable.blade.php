<script type="text/javascript">
    $(document).ready(function () {

        globalThis.table = $('#table_id').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging:false,
            ajax: "{{route('branches.index')}}",
            language: {
                "lengthMenu": "عرض _MENU_ صف في الصفحة",
                "zeroRecords": "لم يتم إيجاد شيء",
                "info": "عرض صفحة _PAGE_ من _PAGES_",
                "infoEmpty": "لا يوجد أي بيانات متاحة",
                "infoFiltered": "(تصفية من _MAX_ العدد الكلي للصفوف)",
                "sSearch": "البحث:"

            },
            columns: [

                {data: 'DT_RowIndex',name:'DT_RowIndex'},
                {data: 'name_ar', name: 'name_ar'},
                

                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]

        });

    });
</script>
