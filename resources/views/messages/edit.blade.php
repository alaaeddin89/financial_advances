@extends('layouts.app_admin')
@section('title','التصنيف الرئيسي')
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
        <div class="col-md-12   ">
            <div class="card">
                <!--begin::Body-->
                <div class="card-body py-1">
                    <!--begin:Form-->
                    <!--begin::Heading-->
                    <div class="mb-13 mt-5 text-start">
                        <!--begin::Title-->
                        <h1 class="mb-3">@yield('title')</h1>
                        <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-gray-400 fw-bold fs-5">
                            <a href="" class="fw-bolder link-primary"> </a>.
                        </div>
                        <!--end::Description-->
                    </div>
                    <form id="form1" class="form" method="POST" action="{{route("categories.update",$super_category->id)}}">
                    @method("put")
                    @csrf
                    <!--begin::Input group-->
                        <div class="row g-9 mb-8">

                           
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">التصنيف</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                       title=" title "></i>
                                </label>
                                <!--end::Label-->
                                <input type="text" name="name" class="form-control " value="{{old("name",$super_category->name)}}">



                            </div>
                            
                           
                            
                           
                           
                            
                        </div>
                        <hr>



                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-6">
                                    <button type="submit" id="user_submit" class="btn btn-primary">
                                        <span class="indicator-label"><i class="fa fa-save"></i> حفظ </span>
                                        <span class="indicator-progress">الرجاء الإنتظار...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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


@endpush
