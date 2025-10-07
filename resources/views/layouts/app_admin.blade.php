<html lang="ar" dir="rtl" direction="rtl">
	<!--begin::Head-->
	<head>
		<title>{{config('app.name')}} | @yield('sub_title')</title>
    	@include('layouts.includes.head')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled page-loading" >
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		@include('layouts.includes.headerMobile')
		<!--end::Header Mobile-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Aside-->
				@include('layouts.includes.aside')
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					<!--begin::Header-->
					@include('layouts.includes.header') 
					<!--end::Header-->
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<!--begin::Subheader-->
						<div class="subheader py-5 py-lg-10 gutter-b subheader-transparent" id="kt_subheader" style="background-color: #663259; background-position: right bottom; background-size: auto 100%; background-repeat: no-repeat; background-image: url({{asset('assets/media/svg/patterns/taieri.svg')}})">
							<div class="container d-flex flex-column" style="color:#ffffff;">
							@include('layouts.includes.Toolbar') 
					
							</div>
						</div>
                        

                        
						<!--end::Subheader-->
						
						<!--begin::Entry-->
						<div class="d-flex flex-column-fluid">
							<!--begin::Container-->
							<div class="container">
								@if ($errors->any())
                                <div class="alert alert-danger" style=" text-align: right;">
                                    <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
								@endif

								

								
								@yield('content')
							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->
					</div>
					<!--end::Content-->
					<!--begin::Footer-->
					@include('layouts.includes.footer') 
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Main-->
		
		<!-- begin::User Panel-->
		@include("modals.userPanel")
		<!-- end::User Panel-->
		
		<!--begin::Sticky Toolbar-->
		@include('layouts.includes.stickyToolbar')
		<!--end::Sticky Toolbar-->
	
		
	</body>
	<!--end::Body-->
</html>