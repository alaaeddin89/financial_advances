<div id="kt_header" class="header bg-white header-fixed">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Left-->
        <div class="d-flex align-items-stretch mr-2">
            <!--begin::Page Title-->
            <h3 class="d-none text-dark d-lg-flex align-items-center mr-10 mb-0">
                شركة الجودة بلس للتدريب
            </h3>
            <!--end::Page Title-->
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                    <ul class="menu-nav">
                        @php $first=0; @endphp
                        @foreach($main as $row)
                            @if(sizeof($row->sub) > 0)
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                    <a href="#" class="menu-link menu-toggle">
                                        <span class="menu-text">{{$row->name}}</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                        <ul class="menu-subnav">
                                            @foreach($row->sub as $row)
                                                @if($row->i_show_menu==0) @continue @endif
                                                <li class="menu-item" aria-haspopup="true">
                                                    @if($row->id==99)
                                                        <a href="{{route($row->url,['account_id'=>-1])}}" class="menu-link">
                                                            <span class="menu-text">{{$row->name}}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{route($row->url)}}" class="menu-link">
                                                            <span class="menu-text">{{$row->name}}</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @php $first+=1; @endphp
                        @endforeach
                    </ul>
                </div>
            </div>
            <!--end::Header Menu Wrapper-->
        </div>
        <!--end::Left-->

        <!--begin::Topbar-->
        <div class="topbar">

            {{-- ✅ إشعارات --}}
            <div class="dropdown">
                <div class="topbar-item mr-3" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg {{ $unreadNotifications->count() > 0 ? 'pulse pulse-danger' : '' }}">
                        <i class="fas fa-bell fs-2"></i>
                        @if($unreadNotifications->count() > 0)
                            <span class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                        @if($unreadNotifications->count() > 0)
                            <span class="pulse-ring"></span>
                        @endif
                    </div>
                </div>
                <div class="dropdown-menu p-0 m-0 dropdown-menu-left dropdown-menu-anim-up dropdown-menu-lg">
                    <div class="d-flex flex-column pt-12 bgi-size-cover bgi-no-repeat rounded-top"
                         style="background-image: url({{asset('assets/media/misc/bg-1.jpg')}})">
                        <h4 class="d-flex flex-center rounded-top">
                            <span class="text-white">الإشعارات</span>
                            <span class="btn btn-text btn-danger btn-sm font-weight-bold btn-font-md ml-2">
                                {{ $unreadNotifications->count() }} جديدة
                            </span>
                        </h4>
                    </div>
                    <div class="p-8">
                        @forelse($unreadNotifications as $notification)
                            <div class="menu-item px-3 mb-2">
                                <a href="{{ $notification->data['link'] ?? '#' }}"
                                   class="menu-link px-3 text-dark text-hover-primary"
                                   onclick="markAsRead(event, '{{ $notification->id }}')">
                                    {!! $notification->data['message'] !!}
                                </a>
                            </div>
                            <div class="separator my-1"></div>
                        @empty
                            <div class="text-center text-muted">لا توجد إشعارات جديدة</div>
                        @endforelse
                        @if($unreadNotifications->count() > 0)
                            <div class="menu-item px-3 mt-3">
                                <a href="#" class="menu-link px-3 text-center text-primary"
                                   onclick="markAllAsRead(event)">
                                    تعليم الكل كمقروء
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ✅ رسائل --}}
            <div class="dropdown">
                <div class="topbar-item mr-3" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg {{ $userInboxMessages->count() > 0 ? 'pulse pulse-primary' : '' }}">
                        <i class="fas fa-envelope-open-text fs-2 text-primary"></i>
                        @if($userInboxMessages->count() > 0)
                            <span class="badge badge-circle badge-primary position-absolute top-0 start-100 translate-middle">
                                {{ $userInboxMessages->count() }}
                            </span>
                        @endif
                        @if($userInboxMessages->count() > 0)
                            <span class="pulse-ring"></span>
                        @endif
                    </div>
                </div>
                <div class="dropdown-menu p-0 m-0 dropdown-menu-left dropdown-menu-anim-up dropdown-menu-lg">
                    <div class="d-flex flex-column pt-12 bgi-size-cover bgi-no-repeat rounded-top"
                         style="background-image: url({{asset('assets/media/misc/bg-1.jpg')}})">
                        <h4 class="d-flex flex-center rounded-top">
                            <span class="text-white">الرسائل</span>
                            <span class="btn btn-text btn-success btn-sm font-weight-bold btn-font-md ml-2">
                                {{$userInboxMessages->count()}} جديدة
                            </span>
                        </h4>
                    </div>
                    <div class="p-8">
                        @forelse($userInboxMessages as $message)
                            <div class="d-flex align-items-center mb-6">
                                <div class="d-flex flex-column font-weight-bold" style="text-align: right;">
                                    <a href="{{route('messages.show',$message->id)}}"
                                       class="text-dark text-hover-primary mb-1 font-size-lg">
                                        {{ $message->sender->name ?? 'مجهول' }}
                                    </a>
                                    <span class="text-muted">{{ Str::limit($message->body, 50) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">لا توجد رسائل جديدة</div>
                        @endforelse
                        <div class="d-flex flex-center pt-7">
                            <a href="{{route('messages.index')}}" class="btn btn-light-primary font-weight-bold text-center">
                                عرض الكل
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ المستخدم --}}
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                    <div class="d-flex flex-column text-right pr-3">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline">{{auth()->user()->role}}</span>
                        <span class="text-dark-75 font-weight-bolder font-size-base d-none d-md-inline">{{auth()->user()->full_name}}</span>
                    </div>
                    <span class="symbol symbol-35 symbol-light-primary">
                        <span class="symbol-label font-size-h5 font-weight-bold">
                            {{ substr(Str::ucfirst(auth()->user()->name),0,1) }}
                        </span>
                    </span>
                </div>
            </div>

        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>


@push('js')
<script>
$(document).ready(function() {
    // --- متغيرات التهيئة ---
    const markOneRoute = '{{ route("notifications.markOne", ["id" => ":id"]) }}';
    const markAllRoute = '{{ route("notifications.markAll") }}';
    const csrfToken = '{{ csrf_token() }}';

    // نستخدم العدادات الموجودة حاليًا في Blade لتهيئة المتغيرات
    let unreadNotifCount = {{ $unreadNotifications->count() }};
    let unreadMsgCount = {{ $userInboxMessages->count() }}; // الرسائل غير المقروءة

    // --- وظيفة مساعدة لتحديث الشريط الرئيسي (Pulse + Badge) ---
    function updateTotalBadge() {
        const totalCount = unreadNotifCount + unreadMsgCount;
        const bellIconContainer = $('.topbar .dropdown:first-child .btn-lg'); // أيقونة الجرس الأولى (الإشعارات)
        const totalBadgeSpan = bellIconContainer.find('.badge-danger');
        const pulseRing = bellIconContainer.find('.pulse-ring');

        // تحديث حالة النبض (Pulse)
        if (totalCount > 0) {
            bellIconContainer.addClass('pulse pulse-danger');
            if (pulseRing.length === 0) {
                bellIconContainer.append('<span class="pulse-ring"></span>');
            }
        } else {
            bellIconContainer.removeClass('pulse pulse-danger');
            pulseRing.remove();
        }

        // تحديث العداد
        if (unreadNotifCount > 0) {
            // تحديث العداد الخاص بالإشعارات
            const notifBadge = $('.topbar .dropdown:first-child .badge-danger');
            notifBadge.text(unreadNotifCount);
            
            // تحديث العنوان داخل الـ dropdown
            $('.dropdown:first-child h4 .btn-danger').text(unreadNotifCount + ' جديدة');
            
        } else {
             // إزالة العداد إذا وصل للصفر
             $('.topbar .dropdown:first-child .badge-danger').remove();
             $('.dropdown:first-child h4 .btn-danger').text('لا توجد إشعارات');
        }
    }


    // --- 1. تعليم إشعار واحد كمقروء (markAsRead) ---
    window.markAsRead = function(event, notificationId) {
        event.preventDefault();
        const linkElement = $(event.currentTarget);
        // الحصول على عنصر الإشعار الأب (menu-item) والفاصل (separator)
        const notificationItem = linkElement.closest('.menu-item');
        const separator = notificationItem.next('.separator');

        $.ajax({
            url: markOneRoute.replace(':id', notificationId),
            method: 'POST',
            data: { _token: csrfToken },
            success: function(response) {
                if (response.success) {
                    // 1. إزالة الإشعار والفاصل من الـ DOM
                    separator.remove(); 
                    notificationItem.remove(); 
                    
                    // 2. تحديث العداد
                    if (unreadNotifCount > 0) {
                        unreadNotifCount--;
                    }
                    updateTotalBadge();

                    // 3. التحقق مما إذا كانت القائمة فارغة
                    if (unreadNotifCount === 0) {
                        $('.dropdown:first-child .p-8').html('<div class="text-center text-muted">لا توجد إشعارات جديدة</div>');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error marking notification as read:', xhr.responseText);
            }
        });
        
        // بعد طلب AJAX، يجب الانتقال إلى رابط الإشعار
        const targetUrl = linkElement.attr('href');
        if (targetUrl && targetUrl !== '#') {
            window.location.href = targetUrl;
        }
    };

    // --- 2. تعليم الكل كمقروء (markAllAsRead) ---
    window.markAllAsRead = function(event) {
        event.preventDefault();
        
        $.ajax({
            url: markAllRoute,
            method: 'POST',
            data: { _token: csrfToken },
            success: function(response) {
                if (response.success) {
                    // 1. تحديث الـ DOM (إزالة القائمة بالكامل)
                    $('.dropdown:first-child .p-8').html('<div class="text-center text-muted">لا توجد إشعارات جديدة</div>'); 
                    
                    // 2. تحديث العداد
                    unreadNotifCount = 0;
                    updateTotalBadge();
                }
            },
            error: function(xhr) {
                console.error('Error marking all notifications as read:', xhr.responseText);
            }
        });
    };

    // --- 3. منع إغلاق الـ dropdown عند النقر داخلها ---
    $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
@endpush