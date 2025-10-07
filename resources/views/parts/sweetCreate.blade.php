<script>
    $(function () {
        $('#form1').on('submit', function (e) {

            e.preventDefault()
            var time = performance.now() - this.startTime;

            //Convert milliseconds to seconds.
            var seconds = time / 1000;
            let timerInterval

            var  url="{{ $route }}"






            var formData = new FormData(this);


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("input[name=_token]").val()
                }
            });

            $.ajax({
                type: 'post',
                url: url,
                data: formData,
                cache: true,
                contentType: false,
                processData: false,

                beforeSend: function(response) {

                    $('.indicator-progress').show();
                    $('.indicator-label').hide()
                    $('#user_submit').prop("disabled", true);

                },

                success: (data) => {

                    $('.indicator-progress').hide();
                    $('.indicator-label').show()
                    $('#user_submit').prop("disabled", false);
                    let message= data.success==true?"success":"error"

                    Swal.fire({
                        position: 'top-center',
                        icon:message,
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1600
                    })

                    if(data.success==true){
                    @if($method!=="put")
                    $(this)[0].reset();
                    @endif
                    }

                }


            })
        })




    })


</script>
