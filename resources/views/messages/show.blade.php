@extends('layouts.app_admin')
@section('title','تفاصيل المراسلة ')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card"  style=" text-align: right;">
                <div class="card-header" style="font-size: calc(1.275rem + 0.3vw);"> مراسلة من خلال نظام الشؤون الإدارية 
                    

                    
                    @if($message->parent_id > 0)
                    <br>
                    رداً على : 
                    <br>

                    <p style="font-size: calc(1.175rem + 0.3vw); color:#FFA800">
                    {{ $message->parent->body ?? null }}
                    </p>
                    @endif
                    

                    

                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif


                    <p style="font-size: calc(1.175rem + 0.3vw);"><strong>مـن</strong> {{ $message->sender->name ?? null }}</p>
                    <p style="font-size: calc(1.175rem + 0.3vw);"><strong>إلـى</strong> {{ $message->receiver->name }}</p>
                    <p style="font-size: calc(1.175rem + 0.3vw);"><strong>نص الرسالة</strong> {{ $message->body }}</p>

                    @if ($message->attachments->count() > 0)
                        <p style="font-size: calc(1.175rem + 0.3vw);"><strong>المرفقات</strong></p>
                        <ul>
                            @foreach ($message->attachments as $attachment)
                                <li>
                                    <a href="{{ asset('storage/'.$attachment->path) }}"
                                     target="_blank">{{ $attachment->filename }}</a>
                                    
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <hr>

                    <h5 style="color:#FFA800"> <strong>الرد على  {{$message->sender->name ?? null }}</strong> </h5>

                    @if ($message->replies->count() > 0)
                        <ul class="list-group">
                            @foreach ($message->replies as $reply)
                                <li class="list-group-item">
                                    <strong>{{ $reply->sender->name }}:</strong>
                                     <strong style ="color:#1BC5BD"> {{ $reply->body }}</strong>
                                    @if ($reply->attachments->count() > 0)
                                        <br>
                                        @foreach ($reply->attachments as $replyAttachment)
                                            <a href="{{ asset('storage/' . $replyAttachment->path) }}" target="_blank">{{ $replyAttachment->filename }}</a>
                                        @endforeach
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>لا يوجد رد</p>
                    @endif

                    <hr>

                    <h5 style="color:#FFA800">رد</h5>

                    <form method="POST" action="{{ route('messages.reply', $message->id) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="message_id" value="{{$message->id }}" >
                        <div class="form-group">
                            <textarea name="body" class="form-control" placeholder="اكتب الرد هنا..." required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="file" name="attachments[]" multiple class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">ارسال الرد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
