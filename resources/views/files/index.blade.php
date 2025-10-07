@extends('layouts.app_admin')
@section('title','تصنيف المؤسسة الفرعي')
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
@endpush
@section('content')


    <!--begin::Tables Widget 13-->
    <div class="card ">
        <!--begin::Card header-->



        <!--end::Card header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="row g-9 mb-8">

                <div class="col-md-3 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                        <span class="required"> اسم  الملف</span>
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                           title=" title "></i>
                    </label>
                    <!--end::Label-->
                    <input id="job_id" type="text" class="form-control"
                           placeholder="اسم الملف"
                           name="file_name"/>
                </div>

                <div class="col-md-3 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                        <span class="required">الملاحظات</span>
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                           title=" title "></i>
                    </label>
                    <!--end::Label-->
                    <input id="note" type="text" class="form-control"
                           placeholder="الملاحظات"
                           name="note"/>
                </div>

                <div class="col-md-3 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                        <span class="required">اختر الموظف</span>
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                           title=" title "></i>
                    </label>
                    <!--end::Label-->
                    <select name="emp_id" id="search-dropdown" class="form-control">

                    </select>

                </div>

                <div class="col-md-3 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                        <span class="required">المؤسسة</span>
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                           title=" title "></i>
                    </label>
                    <!--end::Label-->
                    <select name="category_id" id="" class="form-control">
                        <option hidden value="" selected>يرجي اختيار المؤسسة</option>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach


                    </select>

                </div>
                <div class="col-md-3 fv-row">

                <input type="submit" id="submit" class="btn btn-success" value="بحث">
                </div>

            </div>
            <div class="table-responsive">

            <!--begin::Table-->
                <table id="table_id"
                       class="table table-bordered table-hover table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center fs-7">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bolder  bg-secondary text-muted ">



                        <th class="max-w-40px text-center" ># رقم</th>


                        <th class="min-w-100px text-center" >اسم الملف</th>
                        <th class="max-w-150px text-center" >  اسم الملف الأصلي</th>
                        <th class="min-w-100px text-center" >الملاحظات</th>

                        <th class="max-w-150px text-center">الإجراءات</th>
                    </tr>


                    </thead>


                    </tr>

                    <!--end::Table head-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
    </div>
    <!--end::Tables Widget 13-->
@endsection

@push('js')
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#search-dropdown').select2({
                placeholder: "Search",
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            query: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.full_name
                            }))
                        };
                    },
                    cache: true
                }
            });



        });
    </script>
    @include('files._datatable')
    @include("parts.sweetDelete", ['route' => route('files.destroy', ['file' => ':id'])])
@endpush
