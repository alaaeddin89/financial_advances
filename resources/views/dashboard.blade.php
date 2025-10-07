@extends('layouts.app_admin')
@section('title','الرئيسية')
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
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <!--begin::Card widget - Users Count-->
        @if(in_array(auth()->user()->role, ['accountant']))
            <div class="col-sm-6 col-xl-3 mb-xl-10">
                <div class="card h-lg-100 shadow-sm border-0 hover-elevate-up">
                    <a href="{{ route('users.index') }}" class="stretched-link">
                        <div class="card-body d-flex flex-column align-items-center text-center">
                            
                            <!--begin::Icon-->
                            <div class="symbol symbol-50px symbol-circle mb-4 bg-light-success">
                                <i class="fa fa-user fs-2 text-success"></i>
                            </div>
                            <!--end::Icon-->

                            <!--begin::Number-->
                            <span class="fw-bold fs-2x text-gray-800">{{ $cashier->count() }}</span>
                            <!--end::Number-->

                            <!--begin::Text-->
                            <div class="fw-semibold fs-6 text-gray-600">عدد الموظفين</div>
                            <!--end::Text-->

                            <!--begin::Badge-->
                            <div class="mt-3">
                                <span class="badge badge-light-success fs-7 px-3 py-2">
                                    <i class="bi bi-people me-1"></i>
                                    جميع الموظفين
                                </span>
                            </div>
                            <!--end::Badge-->

                        </div>
                    </a>
                </div>
            </div>
            @endif
            <!--end::Card widget - Users Count-->
        <!--end::Col-->

        <!--begin::Col-->
        @php
            $userRole = auth()->user()->role ?? null; 
            $fullAccessRoles = ['cashier'];
        @endphp
        @if(in_array($userRole, $fullAccessRoles))
        <div class="col-sm-6 col-xl-3 mb-xl-10">
            <!--begin::Card widget-->
            <div class="card h-lg-100 shadow-sm border-0 hover-elevate-up">
                <a href="{{ route('advances.index', ['type_query' => 'advanciesNeedForAproved']) }}" class="stretched-link">
                    <div class="card-body d-flex justify-content-between align-items-center flex-column text-center">
                        
                        <!--begin::Icon-->
                        <div class="symbol symbol-50px symbol-circle mb-4 bg-light-primary">
                            <span class="svg-icon svg-icon-2x svg-icon-primary">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M14 3V21H10V3C10 2.4 10.4 2 11 2H13C13.6 2 14 2.4 14 3ZM7 14H5C4.4 14 4 14.4 4 15V21H8V15C8 14.4 7.6 14 7 14Z" fill="currentColor"></path>
                                    <path d="M21 20H20V8C20 7.4 19.6 7 19 7H17C16.4 7 16 7.4 16 8V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <!--end::Icon-->

                        <!--begin::Number-->
                        <div class="d-flex flex-column mb-2">
                            <span class="fw-bold fs-2x text-gray-800">{{ $advances->count() }}</span>
                        </div>
                        <!--end::Number-->

                        <!--begin::Text-->
                        <div class="fw-semibold fs-6 text-gray-600">عهد بانتظار الموافقة</div>
                        <!--end::Text-->

                        <!--begin::Badge-->
                        <div class="mt-3">
                            <span class="badge badge-light-primary fs-7 px-3 py-2">
                                <i class="bi bi-clock-history me-1"></i>
                                بانتظار المراجعة
                            </span>
                        </div>
                        <!--end::Badge-->

                    </div>
                </a>
            </div>
            <!--end::Card widget-->
        </div>
        @endif
        <!--end::Col-->

        <!--begin::Col-->
    
        <!--end::Col-->
        <div class="col-sm-6 col-xl-3 mb-xl-10">
            <div class="card h-lg-100 shadow-sm border-0 hover-elevate-up">
                <a href="{{ route('messages.index') }}" class="stretched-link">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        
                        <!--begin::Icon-->
                        <div class="symbol symbol-50px symbol-circle mb-4 bg-light-warning">
                            <i class="bi bi-envelope-open fs-2 text-warning"></i>
                        </div>
                        <!--end::Icon-->

                        <!--begin::Number-->
                        <span class="fw-bold fs-2x text-gray-800">{{ $userInboxMessages->count() }}</span>
                        <!--end::Number-->

                        <!--begin::Text-->
                        <div class="fw-semibold fs-6 text-gray-600">المراسلات الواردة</div>
                        <!--end::Text-->

                        <!--begin::Badge-->
                        <div class="mt-3">
                            <span class="badge badge-light-warning fs-7 px-3 py-2">
                                <i class="bi bi-chat-dots me-1"></i>
                                جديدة / غير مقروءة
                            </span>
                        </div>
                        <!--end::Badge-->

                    </div>
                </a>
            </div>
        </div>

        <!--begin::Col-->
        @if(auth()->user()->role == 'admin')
        <div class="col-lg-6">
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">الموظفين حسب التصنيف </h3>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin::Chart-->
                    <div id="chart_pi" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        @endif
        <!--end::Col-->

        <!--begin::Col-->
        @if(auth()->user()->role == 'admin')
        <div class="col-lg-6">
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header h-auto">
                    <!--begin::Title-->
                    <div class="card-title py-5">
                        <h3 class="card-label">إجمالي الرواتب الشهري </h3>
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <div class="card-body">
                    <!--begin::Chart-->
                    <div id="chart_salaries"></div>
                    <!--end::Chart-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        @endif
        <!--end::Col-->
    </div>
    <!--end::Form Widget 13-->
@endsection
@push('js')


<script>

var _demo1 = function () {
		const apexChart = "#chart_salaries";
		var options = {
			series: [{
				name: "إجمالي الرواتب",
				data: <?php echo $chart_salaries_series; ?>
			}],
			chart: {
				height: 350,
				type: 'line',
				zoom: {
					enabled: false
				}
			},
			dataLabels: { 	
				enabled: false
			},
			stroke: {
				curve: 'straight'
			},
			grid: {
				row: {
					colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
					opacity: 0.5
				},
			},
			xaxis: {
				categories: <?php echo $chart_salaries_label; ?>,
			},
			colors: [primary]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}


var _demo12 = function () {
		const apexChart = "#chart_pi";
		var options = {
			series: <?php echo $chart_employees_series ?>,
			chart: {
				width: 380,
				type: 'pie',
			},
			labels: <?php echo $chart_employees_label; ?>,
			responsive: [{
				breakpoint: 480,
				options: {
					chart: {
						width: 200
					},
					legend: {
						position: 'bottom'
					}
				}
			}],
			colors: [primary, success, warning, danger, info]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}  
</script>

<script src="{{asset('assets/js/pages/features/charts/apexcharts.js') }}"></script>

@endpush

