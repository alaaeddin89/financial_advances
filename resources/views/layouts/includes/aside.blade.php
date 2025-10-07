<div class="aside aside-left d-flex flex-column" id="kt_aside">
    <!--begin::Brand-->
    <div class="aside-brand d-flex flex-column align-items-center flex-column-auto py-4 py-lg-8">
        <!--begin::Logo-->
        <a href="{{route('dashboard')}}">
            <img alt="Logo" src="{{asset('assets/media/logos/android-chrome-192x192.png')}}" class="max-h-30px" />
        </a>
        <!--end::Logo-->
    </div>
    <!--end::Brand-->
    <!--begin::Nav Wrapper-->
    <div class="aside-nav d-flex flex-column align-items-center flex-column-fluid pt-7">
        <!--begin::Nav-->
        <ul class="nav flex-column">
            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="الرئيسية">
                <a href="/" class="nav-link btn btn-icon btn-clean btn-icon-white btn-lg active">
                    <i class="flaticon2-protection icon-lg"></i>
                </a>
            </li>
            <!--end::Item-->
       
           
  
            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="إدارة المستخدمين">
                <a href="{{route('users.index')}}" class="nnav-link btn btn-icon btn-icon-white btn-lg" 
                >
                    <i class="flaticon-users-1 icon-lg"></i>
                </a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="إدارة مجموعات المستخدمين">
                <a href="{{route('group')}}" class="nnav-link btn btn-icon btn-icon-white btn-lg" 
                >
                    <i class="fa fa-users"></i>
                </a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
          <!--  <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Finance &amp; Accounting">
                <a href="#" class="nav-link btn btn-icon btn-icon-white btn-hover-text-white btn-lg" data-toggle="tab" data-target="#kt_aside_tab_6" role="tab">
                    <i class="flaticon2-medical-records-1 icon-lg"></i>
                </a>
            </li> -->
            <!--end::Item-->
            <!--begin::Item-->
            
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="ثوابت النظام">
                <a href="{{route('branches.index')}}" class="nav-link btn btn-icon btn-icon-white btn-lg" >
                    <i class="flaticon2-gear icon-lg"></i>
                </a>
            </li> 
            <!--end::Item-->
           
        </ul>
        <!--end::Nav-->
    </div>
    <!--end::Nav Wrapper-->
    <!--begin::Footer-->
    <div class="aside-footer d-flex flex-column align-items-center flex-column-auto py-8">
        
    </div>
    <!--end::Footer-->
</div>