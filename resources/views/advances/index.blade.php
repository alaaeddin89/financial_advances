@extends('layouts.app_admin')
@section('title','كشف العهد ')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">لوحة التحكم</li>
@endsection
@push('css')
    <link href="{{asset('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.dataTables.min.css">

    <style>
        th, td {
            text-align: center;
        }
        input[type="checkbox"] {
            cursor: pointer;
        }
        #table_id {
            border-collapse: collapse;
            width: 100%;
        }

        #table_id, #table_id th, #table_id td {
            border: 1px solid #000;
        }

        .custom-scroll {
            max-height: 800px;
            overflow-y: auto;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }
        .dt-buttons .btn {
            margin-right: 5px;
        }
       

    </style>
@endpush
@section('content')
    

    <div class="row">
    <!--begin::Tables Widget 13-->
        <div class="col-md-12">
            <div class="card" style="text-align: right;">

                <!--begin::Card header-->

                <!--end::Card header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <!--begin::Table container-->
                    <div class="mb-13 mt-5 text-start">
                        <!--begin::Title-->
                        <h1 class="mb-3">@yield('title')</h1>
                        <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-gray-400 fw-bold fs-5">
                        </div>
                        <!--end::Description-->
                    </div>
                    @php
                        $userRole = auth()->user()->role ?? null; 
                        $fullAccessRoles = ['accountant'];
                    @endphp
                    @if(in_array($userRole, $fullAccessRoles))

                    <div class="d-flex justify-content-end" data-table-toolbar="base">

                        <a href="{{route('advances.create')}}" class="btn btn-success "> إضافة عهدة <i class="fa fa-plus"></i></a>

                    </div>
                    @endif


                    

                    <div class="form-group row">
                        @if(in_array($userRole, $fullAccessRoles))
                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الكاشير</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="التصنيف"></i>
                            </label>
                            <!--end::Label-->
                            <select name="user_id" id="" class="form-control">
                                <option hidden value="" selected>يرجى الاختيار</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-2 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">من تاريخ</span>
                            </label>
                            <input type="date" name="date_from" id="date_from" class="form-control" />
                        </div>
                        
                        <div class="col-md-2 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">إلى تاريخ</span>
                            </label>
                            <input type="date" name="date_to" id="date_to" class="form-control" />
                        </div>
                        


                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الحالة</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                   title=" title "></i>
                            </label>
                            <!--end::Label-->
                            <select name="status" id="" class="form-control">
                                <option  disabled selected> يرجي الاختيار</option>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Partially Closed">Partially Closed</option>
                                <option value="Closed">Closed</option>
                            </select>
                            

                        </div>

                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required"> عهد بحاجة الى موافقة</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title=" title "></i>
                            </label>
                            <!--end::Label-->
                            
                            

                            <span class="switch">
                                <label>
                                    <input name="advanciesNeedForAproved"  type="checkbox">
                                    <span></span>
                                </label>
                            </span>
                            
                        </div>

                        <div class="col-lg-3 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                .

                            </label>
                            <button type="submit" id="submit" class="btn btn-primary">
                                <span class="indicator-label"><i class="fa fa-search"></i> بحث </span>
                                
                            </button>
                            <!--<button type="submit" id="receive" class="btn btn-info">
                                <span class="indicator-label"><i class="fa fa-telegram"></i> اعتماد </span>
                                
                            </button> -->

                           
                        </div>

                    </div>

                    
                    

        <div class="table-responsive" >



        <!--begin::Table-->
            <table id="table_id"
                    class="table table-bordered table-hover table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center fs-7">
                <!--begin::Table head-->
                <thead>
                <tr class="fw-bolder  bg-secondary text-muted ">
                    <th class="max-w-10px text-center" >
                        <input type="checkbox" id="select-all">
                    </th>

                    <th class="max-w-40px text-center" ># الرقم</th>
                    <th class="max-w-180px text-center" >اسم الكاشير</th>

                    <th class="min-w-60px text-center" >المبلغ</th>
                    <th class="min-w-50px text-center" >الحالة</th>
                    
                    <th class="min-w-20px text-center">تاريخ الإنشاء</th>
                    <th class="min-w-20px text-center">تاريخ التأكيد</th>
                    <th class="min-w-20px text-center">المبلغ المقفل</th>
                    <th class="min-w-20px text-center">المبلغ المتبقي</th>
                    
                    <th class="min-w-200px text-center"></th>

                </tr>


                </thead>

                <tfoot>
                <tr>
                    <th class="" colspan="3">المجموع</th>
                    <th class="text-center bg-light-danger" id="amount"></th>
                    <th id=""></th>
                    <th id=""></th>
                    <th id=""></th>
                    <th class="text-center bg-light-danger"  id="closed_amount"></th>
                    <th class="text-center bg-light-danger"  id="remaining_balance"></th>
                    <th id=""></th>
                </tr>
                </tfoot>


                <!--end::Table head-->
            </table>


            <!--end::Table-->
        </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
    </div>
</div>



<!-- ============================================== -->
<!-- نافذة التأكيد (AcceptModal) - تمت إضافتها هنا -->
<!-- ============================================== -->
<div class="modal fade" id="AcceptModal" tabindex="-1" aria-labelledby="AcceptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl shadow-2xl">
            <div class="modal-header bg-green-600 text-white rounded-t-xl" style="background-color: #198754;">
                <h5 class="modal-title font-bold text-xl text-white" id="AcceptModalLabel">تأكيد قبول العهدة</h5>
                <!-- زر الإغلاق لـ Bootstrap 5 -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 text-right">
                <p class="text-gray-700 text-lg mb-4">هل أنت متأكد من **قبول العهدة المالية** وتغيير حالتها إلى **مؤكدة (Confirmed)**؟</p>
                <div class="p-3 bg-red-50 text-red-700 border-r-4 border-red-500 rounded-lg" style="border-right-width: 4px; border-style: solid; border-color: #dc3545; background-color: #f8d7da; color: #721c24;">
                    <i class="fas fa-exclamation-triangle me-2"></i> لا يمكن التراجع عن هذا الإجراء بعد التأكيد.
                </div>
                <input type="hidden" id="advance-id-to-confirm">
            </div>
            <div class="modal-footer flex justify-between p-4">
                <button type="button" class="btn btn-secondary bg-gray-300 text-gray-800 hover:bg-gray-400 font-medium py-2 px-4 rounded-lg" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="confirmAcceptBtn" class="btn btn-success bg-green-500 text-white hover:bg-green-600 font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                    <i class="fas fa-check-circle me-2"></i> نعم، تأكيد القبول
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- JSZip & pdfmake (لازم عشان Excel و PDF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
        @include("advances._datatable")
    <script>

        $(document).ready(function () {
           
            $('#statusModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $(this).find("input[name=emp_name]").val(button.data("emp_name"))
                $(this).find("input[name=job_id_no]").val(button.data("job_id_no"))
                $(this).find("input[name=file_number]").val(button.data("file_no"))
                $(this).find("input[name=month]").val(button.data("month"))
                $(this).find("input[name=year]").val(button.data("year"))
                $(this).find("input[name=id]").val(button.data("id"))
                $(this).find("input[name=old_status]").val(button.data("status"))
            });
            $('#returnModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                $("input[name=emp_name]").val(button.data("emp_name"))
                $("input[name=job_id_no]").val(button.data("job_id_no"))
                $("input[name=file_number]").val(button.data("file_no"))
                $("input[name=month]").val(button.data("month"))
                $("input[name=id]").val(button.data("id"))

                $("input[name=year]").val(button.data("year"))
                $("input[name=status]").val(button.data("status"))
            });
            $('#deliveryModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');


                $(this).find("input[name=emp_name]").val(button.data("emp_name"))
                $(this).find("input[name=job_id_no]").val(button.data("job_id_no"))
                $(this).find("input[name=file_number]").val(button.data("file_no"))
                $(this).find("input[name=month]").val(button.data("month"))
                $(this).find("input[name=id]").val(button.data("id"))

                $(this).find("input[name=year]").val(button.data("year"))
                $(this).find("input[name=status]").val(button.data("status"))
            });

            // ==============================================
            // 1. معالجة فتح نافذة التأكيد (AcceptModal)
            // ==============================================
            $('#AcceptModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); 
                // استخراج معرف العهدة المالية من خاصية data-id للزر
                var advanceId = button.data('id'); 
                
                // تخزين المعرف داخل حقل مخفي في المودال لسهولة الوصول إليه لاحقاً
                $('#advance-id-to-confirm').val(advanceId);
                
                console.log('Advance ID set to:', advanceId);
            });

            // ==============================================
            // 2. معالجة الضغط على زر التأكيد داخل المودال
            // ==============================================
            $('#confirmAcceptBtn').on('click', function() {
                var advanceId = $('#advance-id-to-confirm').val();
                
                if (!advanceId) {
                    console.error("لم يتم العثور على معرف العهدة.");
                    // استخدام واجهة مستخدم مخصصة بدلاً من alert
                    // alert('خطأ: لم يتم تحديد العهدة للمتابعة.');
                    // يجب استبدالها بمودال مخصص لإظهار رسائل الخطأ
                    console.error('خطأ: لم يتم تحديد العهدة للمتابعة.'); 
                    return;
                }

                // إغلاق المودال قبل إرسال الطلب
                $('#AcceptModal').modal('hide');

                // المسار الذي يستدعي دالة confirm في الكنترولر (يجب أن يكون Route معرّفاً)
                // تم وضع مسار افتراضي، يجب التأكد من تعريفه في Laravel
                var confirmUrl = `/advances/${advanceId}/confirm`; 
                
                // تنفيذ طلب AJAX
                $.ajax({
                    url: confirmUrl,
                    method: 'POST', 
                    data: {
                        // استخدام Blade Directive لرمز CSRF
                        '_token': '{{ csrf_token() }}' 
                    },
                    success: function(response) {
                        // يمكنك استبدال alert بنظام تنبيهات أفضل (مثل SweetAlert2)
                        // alert(response.message || 'تم تأكيد استلام العهدة بنجاح.'); 
                        console.log('تم تأكيد استلام العهدة بنجاح:', response.message);
                        window.location.reload(); // حل مؤقت في حال عدم توفر متغير DataTables
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'حدث خطأ غير معروف. الرجاء المحاولة مرة أخرى.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                             errorMessage = 'خطأ صلاحيات: غير مصرح لك بتأكيد هذه العهدة.';
                        } else if (xhr.status === 404) {
                             errorMessage = 'خطأ: العهدة المطلوبة غير موجودة.';
                        }
                        
                        // alert('فشل التأكيد: ' + errorMessage);
                        console.error('فشل التأكيد:', errorMessage); 
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل (إذا كان مستخدماً)
                    }
                });
            });



           
            

        });


    </script>


@endpush
