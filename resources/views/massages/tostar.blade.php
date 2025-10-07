@if (session('success'))

    <script>
        toastr.success('{!!session('success')!!}', 'نجاح!', {
            closeButton: true,
            positionClass: "toast-bottom-left",
            "progressBar": true,
            "timeOut": "7000",
        });

    </script>
@elseif(session('error'))

    <script>
        toastr.error('{!!session('error')!!}', 'خطأ!', {
            closeButton: true,
            positionClass: "toast-bottom-left",
            "progressBar": true,
            "timeOut": "7000",
        });

    </script>


@elseif(session('info'))

    <script>
        toastr.info('{!!session('info')!!}', '', {
            closeButton: true,
            positionClass: "toast-bottom-left",
            "progressBar": true,
            "timeOut": "7000",
        });

    </script>

@endif
