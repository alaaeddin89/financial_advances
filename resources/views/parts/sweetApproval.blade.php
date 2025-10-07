<script>

    $("#btn_approval").click(function () {
        Swal.fire({
            title: 'هل أنت متأكد?',
            text: "أنت ستقوم باعتماد هذا التقيم !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#50CD89',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم, أريد الاعتماد!',
            cancelButtonText: "إلغاء",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href="{{route("status.update",['type'=>'3','id'=>$id])}}"

            }
        })
    })


</script>
