

<div class="form-group row">
<div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="d-flex align-items-center fs-6 fw-bold mb-2">
            <span class="required">اسم المستخدم</span>
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
               title=" الاسم الذي يتم استخدامه للدخول لنظام و يجب أن يكون باللغة الإنجليزية "></i>
        </label>
        <!--end::Label-->
        <input id="" type="text" class="form-control form-control-solid"
               placeholder="اسم المستخدم"
               name="name" value="{{old('name',$user->name)}}"/>

    </div>
    <div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="d-flex align-items-center fs-6 fw-bold mb-2">
            <span class="required">الاسم رباعي</span>
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
               title="يرجى إدخال الاسم رباعي"></i>
        </label>

        <!--end::Label-->
        <input id="" type="text" class="form-control form-control-solid"
               placeholder="الاسم رباعي"
               name="full_name" value="{{old("full_name",$user->full_name)}}"/>

    </div>

</div>


<div class="form-group row">
<div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="d-flex align-items-center fs-6 fw-bold mb-2">
            <span class="required">كلمة المرور</span>
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
               title=""></i>
        </label>

        <!--end::Label-->
        <input id="id_number" type="text" class="form-control form-control-solid"
               placeholder="كلمة المرور"
               name="password"/>
    </div>
    <div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="d-flex align-items-center fs-6 fw-bold mb-2">
            <span class="required">البريد الإلكتروني</span>
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
               title="يرجى إدخال البريد الإلكتروني"></i>
        </label>

        <input id="id_number" type="text" class="form-control form-control-solid"
               placeholder="البريد الإلكتروني"
               name="email" value="{{old("email",$user->email)}}"/>

        <!--end::Label-->


    </div>


</div>


<div class="form-group row">
    <div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="d-flex align-items-center fs-6 fw-bold mb-2">
            <span class="required">الصلاحيات</span>
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
               title="يرجى إختيار الصلاحيات الوصول للمستخدم"></i>
        </label>

        <select  name="role"  class="form-control form-control-solid">
            <option  disabled selected>اختيار</option>
            <option {{old('role',$user->role=="admin"?"selected":"") }} value="admin">مدير النظام</option>
            <option {{old('role',$user->role=="accountant"?"selected":"") }} value="accountant"> محاسب</option>
            <option {{old('role',$user->role=="cashier"?"selected":"") }} value="cashier">كاشير</option>
           
            
        </select>


        <!--end::Label-->


    </div>

    <div class="col-md-6 fv-row">
    <!--begin::Label-->
    <label class="d-flex align-items-center fs-6 fw-bold mb-2">
        <span class="required">اختر مجموعة الصلاحيات</span>
        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
            title=" title "></i>
    </label>
    <!--end::Label-->
    <select name="my_multi_select1[]" id="mySelect" multiple class="form-control">
        
            <?php foreach($gruop as $row) {?> 
            <option
                value='<?php echo $row->id ?>' 
                <?php echo in_array($row->id,$arr)?"selected":"";?>>
                <?php echo $row->name ?>
            </option>
            <?php } ?>

    </select>

</div>
</div>






    
    


