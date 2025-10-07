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
 <!--begin::Form Widget 13-->
 <div class="row">
        <div class="col-md-12   ">
                <div class="card" style="text-align: right;">
                    <!--begin::Body-->
                    <div class="card-body py-1">
                        <!--begin:Form-->
                        <!--begin::Heading-->
                        <div class="mb-13 mt-5 text-start">
                            <!--begin::Title-->
                            <h1 class="mb-3">{{$navTitle}}</h1>
                            <!--end::Title-->
                           
                        </div>
                        <form id="form1" class="form" method="POST" action="{{route('doPergroup')}}">
                        @csrf
                        <!--begin::Input group-->
                            

                            <div class="form-group row">
                                
                                <div class="col-md-6 fv-row">
                                  <h3 >Select tools</h3> 
                                    <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]">
                                        <?php foreach($tool as $row) {?>
                                            <optgroup label="<?php echo $row->name ?>">
                                            
                                        <?php foreach($row->sub as $row1) {?>
                                            <option value='<?php echo $row1->id ?>'
                                                <?php echo in_array($row1->id,$arr)?"selected":"";?>>
                                                <?php echo $row1->name ?>
                                            </option>
                                            <?php } }?>
                                    </select>
                                    
                                  <input type="hidden" name="id" class="form-control" id="id" value="{{ $group->id }}" >

                                </div>

                                <div class="col-md-6 fv-row">
                                  
                                  <h3>Select Users</h3> 
                                  <select multiple="multiple" class="multi-select" id="my_multi_select2" name="my_multi_select2[]">
                                      <?php foreach($user as $row1) {?>
                                          <option
                                              value='<?php echo $row1->id ?>'
                                              <?php echo in_array($row1->id,$arr1)?"selected":"";?>>
                                              <?php echo $row1->name ?>
                                          </option>
                                          <?php
                                      }?>
                                  </select> 
                                </div>

                              
                            </div>


                           
                    
                            <div class="card-footer">
                                <div class="row">
                                    
                                    <div class="col-lg-6">
                                        <button type="submit" id="user_submit" class="btn btn-primary">
                                            <span class="indicator-label"><i class="fa fa-save"></i>  حفظ </span>
                                            
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
    $(document).ready(function(){
        $('.multi-select').multiSelect();

        $('#my_multi_select2').multiSelect({
		  selectableHeader: "<div class='custom-header' style='color:#4267B2'><b>موظفون خارج المجموعة</b></div>",
		  selectionHeader: "<div class='custom-header' style='color:#4267B2'><b>موظفون داخل المجموعة </b></div>"
		});

        $('#my_multi_select1').multiSelect({
		  selectableHeader: "<div class='custom-header' style='color:#4267B2'><b>الصلاحيات المحجوبة</b></div>",
		  selectionHeader: "<div class='custom-header' style='color:#4267B2'><b>الصلاحيات الممنوحة</b></div>"
		});
    })


    
    
</script>    

@endpush














