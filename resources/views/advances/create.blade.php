@extends('layouts.app_admin')
@section('title','تسجيل عهدة جديدة')
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
            <div class="card" style="text-align: right;">
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
                            <a href="{{route('advances.index')}}" class="fw-bolder link-primary">جميع العهد</a>.
                        </div>
                        <!--end::Description-->
                    </div>
                    <form method="POST" action="{{ route('advances.store') }}">
                    @csrf
                    @method('POST')
                    <!--begin::Input group-->

                        <div class="form-group row">
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">اختر الكاشير</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title=" title "></i>
                                </label>
                                <!--end::Label-->

                                <select name="user_id" id="" class="form-control">
                                    <option hidden value="" selected>يرجي اختيار الكاشير</option>
                                    @foreach($employees as $employee )
                                        <option  value="{{$employee->id}}">{{$employee->full_name}}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6  mb-2">
                                    <span>المبلغ</span>

                                </label>
                                <!--end::Label-->
                                <input id="" type="text" class="form-control"
                                        placeholder="المبلغ"

                                        name="amount"/>
                            </div>

                                
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6  mb-2">
                                    <span> ملاحظات </span>

                                </label>
                                <!--end::Label-->
                                <input id="" type="text" class="form-control"
                                        placeholder="ملاحظات"
                                        name="description"/>
                            </div>

                        </div>

                        
                
                        <div class="card-footer">
                            <div class="row">

                                <div class="col-lg-5">
                                    <button type="submit" id="user_submit" class="btn btn-primary">
                                        <span class="indicator-label"><i class="fa fa-save"></i> حفظ </span>
                                    
                                    </button>
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

        <div class="col-md-12">


        </div>
    </div>
    <!--end::Form Widget 13-->
@endsection
@push('js')
    <script>
       
    </script>

@endpush
