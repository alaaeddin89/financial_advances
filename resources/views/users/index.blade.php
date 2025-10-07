@extends('layouts.app_admin')
@section('title','عرض المستخدمين')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">لوحة التحكم</li>
@endsection
@push('css')


    <link href="{{asset('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet"
          type="text/css"/>
@endpush
@section('content')


    <!--begin::Tables Widget 13-->
    <div class="card ">
        <!--begin::Card header-->



        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                @yield("title")

            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-table-toolbar="base">

                    <a href="{{route('users.create')}}" class="btn btn-success "> إضافة مستخدم <i class="fa fa-plus"></i></a>
{{--                    <select name="status" data-control="select2" data-hide-search="true" data-placeholder="Export" class="form-select form-select-solid ">--}}
{{--                        <option value="1">Excel</option>--}}
{{--                        <option value="1">PDF</option>--}}
{{--                        <option value="2">Print</option>--}}
{{--                    </select>--}}
                </div>
                <!--end::Toolbar-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-table-toolbar="selected">
                    <div class="fw-bolder me-5">
                        <span class="me-2" data-table-select="selected_count"></span>
                    </div>
                    <!--<button type="button" class="btn btn-danger" data-table-select="delete_selected">احذف المحدد
                    </button> -->
                </div>
                <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">

            <!--begin::Table-->
                <table id="table_id"
                       class="table table-bordered table-hover table-row-gray-300 align-middle gs-0 gy-3 border-1 text-center fs-7">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bolder  bg-secondary text-muted ">
                        <th class="w-10 text-center"  style="width:1%">#</th>
                        <th class="w-10  text-center"  style="width:10%">الإسم المستخدم</th>
                        <th class="w-10  text-center" style="width:15%">البريد الإلكتروني</th>

                        <th class="w-10  text-center" style="width:10%">الدور</th>
                        <th class="min-w-100px text-center" style="width:10%">تاريخ الإضافة</th>
                        <th class="min-w-124px text-center" style=" width:30%">الإجراءات</th>

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

    @include('users._datatable')
    @include("parts.sweetDelete", ['route' => route('users.destroy', ['user' => ':id'])])
@endpush
