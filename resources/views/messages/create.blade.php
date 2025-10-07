@extends('layouts.app_admin')
@section('title','رسالة جديدة')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <!--begin::Item-->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>

    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection
@section('content')
    
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style=" text-align: right;">مراسلة جديدة</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="receiver_id" class="col-md-4 col-form-label text-md-right">ااـى :</label>

                            <div class="col-md-6">
                                <select id="receiver_id" class="form-control @error('receiver_id') is-invalid @enderror" name="receiver_id" required>
                                    <option value="">اختر المستقبل </option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                @error('receiver_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="body" class="col-md-4 col-form-label text-md-right">اكتب رسالتك</label>

                            <div class="col-md-6">
                                <textarea id="body" class="form-control @error('body') is-invalid @enderror" name="body" required>
                                
                                </textarea>

                                @error('body')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="attachments" class="col-md-4 col-form-label text-md-right">المرفقات</label>

                            <div class="col-md-6">
                                <input type="file" id="attachments" class="form-control-file @error('attachments.*') is-invalid @enderror" name="attachments[]" multiple>

                                @error('attachments.*')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    حفظ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')


@endpush
