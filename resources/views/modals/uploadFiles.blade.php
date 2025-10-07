<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    أنقر هنا لتحميل الملفات
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">أرفق الملفات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="uploadImageLine" style="position:relative; width:100%;">

            </div>


            <div class="alert alert-danger print-error-msg m-10" style="display:none">
                <ul></ul>
            </div>
            <form method="post" enctype="multipart/form-data" class="form-horizontal" id="laravel-ajax-file-upload"
                  action="javascript:void(0)">
                @csrf

                <div class="modal-body">


                    <div class="row form-group">
                        <div class="col-12 col-md-12 inputs-form">
                            <div class="control-group" id="fields">
                                <label class="control-label" for="field1">
                                </label>
                                <div class="controls">
                                    <div class="entry input-group upload-input-group">
                                        <input class="form-control" name="files[]" type="file">
                                        <button class="btn btn-upload btn-success btn-add" type="button">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>


                        </div>

                        <div class="col-12">
                            <table
                                class="table  table-bordered table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center">

                                <thead>
                                <tr>
                                    <td>اسم الملف</td>
                                    <td>حالة التحميل</td>
                                </tr>
                                </thead>

                                <tbody class="statuses">


                                </tbody>

                            </table>
                        </div>


                        <div class="col-12 alert-warning p-3">
                            <ul>
                                <li style="line-height:20px">يجب أن لا يتجاوز حجم الملفات 2 ميجا.</li>
                                <li style="line-height:20px"> أنواع الملفات التي يمكن تحميلها (pdf,xsl) فقط.</li>
                            </ul>
                        </div>


                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <input type="submit" class="btn btn-primary" class="upload" value="تحميل">
                </div>

            </form>
        </div>
    </div>
</div>

@push('js')
    <script>


        $(function () {
            $(document).on('click', '.btn-add', function (e) {
                e.preventDefault();

                var controlForm = $('.controls:first'),
                    currentEntry = $(this).parents('.entry:first'),
                    newEntry = $(currentEntry.clone()).appendTo(controlForm);

                newEntry.find('input').val('');
                controlForm.find('.entry:not(:last) .btn-add')
                    .removeClass('btn-add').addClass('btn-remove')
                    .removeClass('btn-success').addClass('btn-danger')
                    .html('<span class="fa fa-trash"></span>');
            }).on('click', '.btn-remove', function (e) {
                $(this).parents('.entry:first').remove();

                e.preventDefault();
                return false;
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $('#laravel-ajax-file-upload').submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({

                    xhr: function () {
                        $('.uploadImageLine').html(` <div class="progress-bar" role="progressbar" aria-valuenow=""
                        aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            0%
                        </div>`);
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = (evt.loaded / evt.total) * 100;
                                $('.progress-bar').text(Math.floor(percentComplete) + '%');
                                $('.progress-bar').css('width', Math.floor(percentComplete) + '%');
                                if (Math.floor(percentComplete) == 100) {
                                    $('.progress-bar').text('جاري تحميل الملفات');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: "{{route('utilization.order.file.uploadFiles')}}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {

                        this.reset();

                        if (data.error) {

                            $(".print-error-msg").find("ul").html('');
                            $(".print-error-msg").css('display', 'block');

                            $.each(data.error, function (key, value) {
                                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                            });

                            $('.progress-bar').text('فشلت عملية تحميل الملفات');
                            $('.progress-bar').css('background-color', 'red');
                            $('.statuses').css("display", "");
                        } else if (!jQuery.isEmptyObject(data)) {

                            $('.progress-bar').text('تم بنجاح');
                            $('.progress-bar').css('background-color', 'green');
                            $('.statuses').css("display", "");
                            $(".print-error-msg").css('display', 'none');

                            var data = jQuery.parseJSON(data);

                            $.each(data, function (key, value) {

                                if (value == 'success') {
                                    $('.statuses').append(`   <tr>
                                    <td> ` + key + `</td>
                                    <td> <i class="fa fa-check  fa-lg" style="color: green;"> </i> </td>
                                </tr>`)
                                } else {
                                    $('.statuses').append(`   <tr>
                                    <td> ` + key + `</td>

                                    <td> <i class="fa fa-window-close  fa-lg" style="color: green;"> </i> </td>
                                </tr>`)

                                }


                            });
                        } else {

                            $('.progress-bar').text('لم يتم تحميل أي ملفات');


                        }
                    },
                });

                $('.modal').on('hide.bs.modal', function () {
                    $(".progress-bar").hide();
                    $(".statuses").hide();
                    $(".print-error-msg").hide();


                });


            });
        });

    </script>

@endpush
