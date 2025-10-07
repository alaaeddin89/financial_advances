<html lang="ar" dir="rtl" direction="rtl">
<!--begin::Head-->
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="{{asset('assets/plugins/global/plugins.bundle.rtl.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/style.bundle.rtl.css')}}" rel="stylesheet" type="text/css"/>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed"
      style="--kt-toolbar-height:65px;--kt-toolbar-height-tablet-and-mobile:55px">
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    @yield('content')
</div>
<!--end::Root-->
<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Custom Javascript(used by this page)-->
@stack('js')
<script src="{{asset('assets/js/custom/widgets.js')}}"></script>

<!--end::Page Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>
