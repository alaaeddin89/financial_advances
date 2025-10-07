@extends('layouts.app_admin')
@section('title','إضافة مندوب المؤسسة الفرعية')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection
@section('content')
    <!--begin::Form Widget 13-->
    <div class="row">
         <div class="col-md-12">
        <div class="card">
            <!--begin::Body-->
            <div class="card-body py-1">
                <!--begin:Form-->
                <!--begin::Heading-->
                <div class="mb-13 mt-5 text-start">
                    <!--begin::Title-->
                    <h1 class="mb-3">اضافة مرفق </h1>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="text-gray-400 fw-bold fs-5">
                        <a href="" class="fw-bolder link-primary"> </a>.
                    </div>
                    <!--end::Description-->
                </div>
                <form id="form1" class="form" method="POST" action="{{route("files.store")}}" enctype="multipart/form-data">

                @csrf
                <!--begin::Input group-->
                    <div class="form-group row">

                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">اختر الموظف</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"title=" title "></i>
                            </label>
                            <!--end::Label-->
                            <select name="emp_id" id="search-dropdown" class="form-control">

                             
                            </select>
                        </div>
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">اختر المؤسسة</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                   title=" title "></i>
                            </label>
                            <!--end::Label-->

                            <select name="category_id" id="" class="form-control">
                                <option hidden value="" selected>يرجي اختيار المؤسسة</option>
                                @foreach($categories as $category )
                                    <option data-sps="{{$category->salary_percent_status}}" data-sl="{{$category->salary_limit}}"  data-cc="{{$category->cash_currency}}" data-sc="{{$category->salary_currency}}" value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">المرفق</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                   title=" title "></i>
                            </label>
                            <!--end::Label-->
                            <input type="file" name="file"  class="form-control">



                        </div>

                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">الملاحظات </span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                   title=" title "></i>
                            </label>
                            <!--end::Label-->
                            <input type="text" name="note" class="form-control form-control-solid">


                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">اسم  المرفق </span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                   title=" title "></i>
                            </label>
                            <!--end::Label-->
                            <input type="text" name="file_name" class="form-control form-control-solid">


                        </div>

                    </div>
                    <hr>



                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-6">
                                <button type="submit" id="user_submit" class="btn btn-primary">
                                    <span class="indicator-label"><i class="fa fa-save"></i> حفظ </span>
                                </button>
                                <a href="" class="btn btn-secondary"> <i class="fa fa-"></i>عودة</a>
                                <button type="reset" id="user_cancel" class="btn btn-white me-3">إلغاء</button>
                            </div>
                        </div>
                    </div>




                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--begin::Body-->
        </div>
    </div>
    </div>

    <!--end::Form Widget 13-->
@endsection
@push('js')
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

@endpush
