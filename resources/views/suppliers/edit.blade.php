@extends('layouts.app_admin')
@section('title','تعديل الموردين ')
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
                    <form id="form1" class="form" method="POST" action="{{route("suppliers.update",$supplier->id)}}">
                    @method("put")
                    @csrf
                    <!--begin::Input group-->
                    <div class="form-group row">

                        <div class="col-md-5 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                الإسم 
                            </label>
                            <!--end::Label-->
                            <input type="text" name="name" id="name"  value="{{old("name",$supplier->name)}}" class="form-control" >

                        </div>

                        <div class="col-md-5 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                للاتصال 
                            </label>
                            <!--end::Label-->
                            <input type="text" name="contact_person" id="contact_person" value="{{old("name",$supplier->contact_person)}}" class="form-control" >

                        </div>
                    </div>
                    <div class="form-group row">    


                        <div class="col-md-5 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                الهاتف 
                            </label>
                            <!--end::Label-->
                            <input type="text" name="phone" id="phone" value="{{old("name",$supplier->phone)}}"  class="form-control " >

                        </div>

                        <div class="col-md-5 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                العنوان
                            </label>
                            <!--end::Label-->
                            <input type="text" name="address" id="address" value="{{old("name",$supplier->address)}}"  class="form-control " >

                        </div>

                    </div>


                  



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


@endpush
