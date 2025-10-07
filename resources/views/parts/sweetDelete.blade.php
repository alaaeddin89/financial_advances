<script>
    $(document).on('click', '.delete', function(e) {

        e.preventDefault();

        var id = $(this).data('id')
        var url = "{{ $route }}";
        url = url.replace(':id', id)

        Swal.fire({
            title: 'هل أنت متأكد?',
            text: "أنت ستقوم بحذف هذا السجل !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم, أريد الحذف!',
            cancelButtonText: "إلغاء",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {
                        id: $(this).val(),

                    },
                    success: function(result) {
                        if (result.status =="success") {

                            Swal.fire(
                                'تم الحذف!',
                                'تم حذف هذا السجل.',
                                'success'
                            )
                        };

                        if (result.status =="error") {

                            Swal.fire({
                                position: 'top-center',
                                icon:result.status ,
                                text: result.message,
                                showConfirmButton: false,
                                timer: 6200
                            })
                        };
                        table.ajax.reload();
                    },
                    error: function(result) {
                        alert('error');
                        console.log(result);
                    }
                });

            }
        })




    })
</script>
