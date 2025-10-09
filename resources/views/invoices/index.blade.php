@extends('layouts.app_admin')
@section('title','كشف الفواتير ')
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
                        $fullAccessRoles = ['cashier'];
                    @endphp
                    @if(in_array($userRole, $fullAccessRoles))

                    <div class="d-flex justify-content-end" data-table-toolbar="base">

                        <a href="{{route('invoices.create')}}" class="btn btn-success "> إضافة فاتورة <i class="fa fa-plus"></i></a>

                    </div>
                    @endif


                    

                    <div class="form-group row">
                        @if(in_array($userRole, ['accountant']))
                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الكاشير</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="التصنيف"></i>
                            </label>
                            <!--end::Label-->
                            <select name="user_id" id="" class="form-control">
                               <option value="">الكل</option>
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
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span>حالة الإغلاق</span>
                            </label>
                            <select name="closure_status" id="closure_status" class="form-control">
                                <option value="" selected>الكل</option>
                                <option value="open">غير مغلقة</option>
                                <option value="closed_pending">مغلقة بدون موافقة</option>
                                <option value="closed_approved">مغلقة وموافق عليها</option>
                            </select>
                        </div>


                        

                        <div class="col-lg-3 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                .

                            </label>
                            <button type="submit" id="submit" class="btn btn-primary">
                                <span class="indicator-label"><i class="fa fa-search"></i> بحث </span>
                                
                            </button>
                        </div>

                    </div>

                    
            
        <div class="table-responsive" >



        <!--begin::Table-->
            <table id="table_id"
                    class="table table-bordered table-hover table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center fs-7">
                <!--begin::Table head-->
                <thead>
                <tr class="fw-bolder  bg-secondary text-muted ">
                    
                    <th class="max-w-40px text-center" ># الرقم</th>
                    <th class="max-w-180px text-center" >رقم الفاتورة</th>

                    <th class="min-w-60px text-center" >المورد</th>
                    <th class="min-w-50px text-center" >المبلغ</th>
                    
                    <th class="min-w-20px text-center"> حالة الفاتورة</th>
                    <th class="min-w-20px text-center">تاريخ الفاتورة</th>
                    <th class="min-w-20px text-center">المبلغ المقفل</th>
                    <th class="min-w-50px text-center">حالة الإغلاق</th>
                    <th class="max-w-180px text-center" >الكاشير</th>
                    <th class="min-w-200px text-center"></th>

                </tr>


                </thead>

                <tfoot>
                <tr>
                    <th class="" colspan="2">المجموع</th>
                    <th id=""></th>
                    <th class="text-center bg-light-danger" id="amount"></th>
                    <th id=""></th>
                    <th id=""></th>
                    <th class="text-center bg-light-danger" id="closed_amount"></th>
                    <th id=""></th>
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

        @include("invoices._datatable")
    <script>

        $(document).ready(function () {
            
           
            

        });

        function rejectInvoice(id) {
            if(!confirm('هل أنت متأكد من رفض هذه الفاتورة؟')) return;

            $.ajax({
                url: '/invoices/' + id + '/reject',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.success);
                    $('#invoices_table').DataTable().ajax.reload();
                    // إعادة تحميل DataTable بعد التغيير
                    $('#table_id').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'حدث خطأ');
                }
            });
        }

        $(document).ready(function () {
            // تحديد القيم الافتراضية لحقلَي التاريخ عند تحميل الصفحة
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

            const formatDate = (date) => {
                const year  = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day   = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            $('#date_from').val(formatDate(firstDay));
            $('#date_to').val(formatDate(today));
        });

    </script>


@endpush
