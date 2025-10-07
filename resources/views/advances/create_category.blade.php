@extends('layouts.app_admin')
@section('title','احتساب الرواتب حسب التصنيف')
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
                        </div>
                        <!--end::Description-->
                    </div>
                    <form method="POST" action="{{ route('salary.store_category') }}">
                    @csrf
                    @method('POST')

                    <div class="form-group row">
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">اختر التصنيف</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title=" title "></i>
                            </label>
                            <!--end::Label-->

                            <select name="type_id" id="" class="form-control">
                                <option hidden value="" selected>يرجي اختيار التصنيف</option>
                                @foreach($types as $type )
                                    <option  value="{{$type->id}}">{{$type->names}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6  mb-2">
                                <span>شهر</span>

                            </label>
                            <!--end::Label-->
                            <input id="" required type="text" class="form-control"
                                    placeholder="شهر"

                                    name="month"/>
                        </div>

                        
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6  mb-2">
                                <span>سنة</span>

                            </label>
                            <!--end::Label-->
                            <input id="" required type="text" class="form-control"
                                    placeholder="سنة"

                                    name="year"/>
                        </div>
                        

                    </div>
                    <div class="form-group row">
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6  mb-2">
                                <span>رقم الدفعة </span>

                            </label>
                            <!--end::Label-->
                            <input id="" type="text" class="form-control form-control-solid"
                                    placeholder="رقم الدفعة"
                                    readonly
                                    name="file_no"/>
                        </div>

                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">سعر الصرف</span>
                                <i class="fas  ms-2 fs-7" data-bs-toggle="tooltip"
                                    title=" يرجى ااضافة سعر الصرف "></i>
                            </label>
                            <!--end::Label-->

                            <input id="" type="text" class="form-control" value = "{{$price}}"
                                    name="price"/>
                        </div>
                        

                    </div>
                    
                    <div class="form-group row">


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

   
    </div>
    <!--end::Form Widget 13-->
@endsection
@push('js')
    <script>
        $(document).ready(function () {

            $('select[name=type_id]').change(function () {
            
                var masterId = $(this).val();
                if (masterId) {
                    $.ajax({
                        url: '{{route("fileNo")}}',
                        method: 'GET',
                        data: { 
                            master_id: masterId,
                            type_id : 'all_category',
                         },
                        success: function (response) {
                            console.log(response);
                            $('input[name=file_no]').val(response.data);
                        },
                        error: function () {
                            $('input[name=file_no]').val(0);
                        }
                    });

                } else {
                    $('#fileNoDisplay').text('يرجى اختيار تصنيف.');
                }
            });
        });
    </script>

@endpush
