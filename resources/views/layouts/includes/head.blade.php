<meta charset="utf-8"/>
<meta name="description" content=""/>
<meta name="keywords" content=""/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<link rel="canonical" href="{{config('app.url')}}"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="shortcut icon" href="{{asset("images/logo.png")}}">
<!--begin::Fonts-->

<link rel="stylesheet" href="{{asset("assets/css/toastr.css")}}">
<!--end::Global Stylesheets Bundle-->
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet" />


<!--begin::Page Vendors Styles(used by this page)-->
<link href="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/plugins/custom/leaflet/leaflet.bundle.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Page Vendors Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="{{asset('assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/css/style.bundle.css?v=7.2.8')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{asset('assets/ac/jquery-ui.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="{{asset('assets/media/logos/favicon.ico')}}" />
  <link rel="stylesheet" href="{{asset('assets/build/css/intlTelInput.css')}}">
  <link rel="stylesheet" href="{{asset('assets/build/css/demo.css')}}">

<link href="{{  asset('assets/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet"> 
  

<!--end::Fonts-->
<!--begin::Page Custom Styles(used by this page)-->
<link href="{{asset('assets/css/pages/login/login-4.css')}}" rel="stylesheet" type="text/css" />


@stack('css')
<style>
    @font-face {
        font-family: 'Cairo-Regular';
        font-style: normal;
        src: url('/assets/font/Cairo-Regular.ttf');

    }

    html, body{

        font-family: Cairo-Regular,"sans-serif" !important;

    }
    
    body{
			font-family: "Cairo", sans-serif, Helvetica;
		}
		.ms-container{
			width:100%;
		}
	/*	.modal .modal-header {
			-webkit-box-align: center;
			-ms-flex-align: center;
			align-items: center;
			background-color: #8950FC;
		}
.modal .modal-header .modal-title {
    font-weight: 500;
    font-size: 1.3rem;
    color: #ffffff;
} 
.lbl_bold{
	font-weight: bold !important;
	color:#8950FC !important;
} */
.ui-widget.ui-widget-content{
z-index:100000
}
.select2-container{
    width: 100% !important;
}
select.select2{
	    opacity: 1;
}

@media (max-width: 768px) {
  #kt_header_mobile .burger-icon {
    margin-left: 12px !important; /* زيادة التباعد بين الأزرار */
  }
  #kt_header_mobile #kt_header_mobile_topbar_toggle {
    margin-left: 16px !important; /* مسافة أكبر لزر المستخدم */
  }
  #kt_header_mobile .d-flex.align-items-center {
    gap: 10px; /* يجعل الأزرار متباعدة تلقائياً */
  }
}

.label {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 12px;         /* حشوة كافية للنص */
  border-radius: 6px;        /* زوايا ناعمة */
  font-size: 0.95rem;        /* حجم خط متوسط */
  font-weight: 600;          /* خط واضح */
  line-height: 1.4;
  white-space: nowrap;       /* يمنع انكسار الكلمة */
  min-width: auto;           /* يمنع أي عرض ثابت */
  width: auto;               /* يجعلها تتسع للنص */
}

.label-success {
  background-color: #28a745;
  color: #fff;
}

.label-warning {
  background-color: #ffc107;
  color: #212529;
}

.label-secondary {
  background-color: #6c757d;
  color: #fff;
}

    
    </style>
