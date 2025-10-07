<ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">

			
			
			<!--begin::Item-->
			<li class="nav-item mb-2" data-toggle="tooltip" title="مراسلة جديدة" data-placement="left">
				<a class="btn btn-sm btn-icon btn-bg-light btn-icon-warning btn-hover-warning" href="{{route('messages.create')}}">
					<i class="flaticon2-telegram-logo"></i>
				</a>
			</li>
			<!--end::Item-->



			



			

			<!--begin::Item-->
			<li class="nav-item mb-2" data-toggle="tooltip" title="تسجيل الخروج" data-placement="left">
				<a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger" href="{{route('logout')}}"
				onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
					<i class="fas fa-sign-out-alt"></i>
				</a>
				
			</li>
			<!--end::Item-->


			

		
		</ul>