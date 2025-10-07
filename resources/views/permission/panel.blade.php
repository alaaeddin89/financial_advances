@extends('layouts.app_admin')
@section('title','إدارة المجموعات')
@section('toolbar.title','لوحة التحكم')

@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">لوحة التحكم</li>
@endsection
@push('css')

@endpush
@section('content')


<div class="row">

  <!--begin::Tables Widget 13-->
      <div class="col-md-12">
          <div class="card style="text-align: right;">

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
                  <div class="row g-9 mb-8">

                    <main class="col-md-9 ms-sm-auto col-lg-12 px-md-4"> 
                      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"> 
                        <form method="post" action="{{route('doGroup')}}">
                          @csrf 

                            <div class="mb-3">
                              <label for="floatingInput">اسم المجموعة </label>
                              <input type="text" name="name" id="name" class="form-control"  >
                              <input type="hidden" name="id" class="form-control" id="id" >
                            </div> 

                            <div class="checkbox mb-3" style="display: none">
                              <label>
                                <input type="checkbox" checked name="b_enabled" id="b_enabled" value="remember-me"> Active?
                              </label>
                            </div>
                            <button class="btn btn-lg btn-primary" type="submit">Save</button> 
                          </form>
                        
                      </div> 
                      <div class="table-responsive"> 
                        <table class="table table-striped table-sm">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">إسم المجموعة </th> 
                              <th scope="col">إضافة الصلاحيات </th>
                              <th scope="col">تعديل المجموعة </th>
                              <th scope="col">حذف </th>
                            </tr>
                          </thead>
                          <tbody>
                          @php
                          $i = 1
                          @endphp
                            @foreach($group as $row)
                            <tr>
                              <td>{{$i }}</td>
                              <td>{{$row->name}}</td> 
                              <td>
                                <a href="{{url('')}}/pergroup/{{$row->id}}">
                                  <i class="fa fa-users"></i>
                                </a>
                              </td> 
                              <td>
                                <a href="javascript:void(0)" onclick="$('#id').val('{{$row->id}}');$('#name').val('{{$row->name}}');">
                                <i class="fa fa-edit"></i>
                                </a>
                              </td> 
                              <td>
                                <form method="post" action="{{route('delGroup')}}" onsubmit="return confirm('are you sure?')">
                                @csrf  
                                    <input type="hidden" name="id" class="form-control" id="id" value="{{$row->id}}"> 
                                      <a href="javascript:void(0)" onclick="$(this).parent().trigger('submit')"> 
                                      <i class="fa fa-remove"></i> 
                                      </a>
                                </form>
                              </td>
                            </tr>
                            @php
                            $i++
                            @endphp
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </main>
                  
                  

                  </div>

                
                  <!--end::Table container-->
              </div>
              <!--begin::Body-->
          </div>
      </div>
  </div>

@endsection

@push('js')
  
    

@endpush

