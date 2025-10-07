@extends('layouts.app_admin')
@section('title','تفاصيل المورد')
@section('toolbar.title','لوحة التحكم')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('suppliers.index') }}">كشف الموردين</a>
    </li>
    <li class="breadcrumb-item text-muted">@yield('title')</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">تفاصيل المورد</h3>
        <div class="card-toolbar">
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-arrow-left"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>اسم المورد (عربي)</th>
                <td>{{ $supplier->name_ar }}</td>
            </tr>
            <tr>
                <th>اسم المورد (إنجليزي)</th>
                <td>{{ $supplier->name_en }}</td>
            </tr>
            <tr>
                <th>الرقم الضريبي</th>
                <td>{{ $supplier->tax_id_no }}</td>
            </tr>
            <tr>
                <th>السجل التجاري</th>
                <td>{{ $supplier->commercial_register_no }}</td>
            </tr>
            <tr>
                <th>الهاتف</th>
                <td>{{ $supplier->phone }}</td>
            </tr>
            <tr>
                <th>العنوان الوطني</th>
                <td>{{ $supplier->national_address }}</td>
            </tr>
            <tr>
                <th>رقم المبنى</th>
                <td>{{ $supplier->building_number }}</td>
            </tr>
            <tr>
                <th>الرقم الفرعي</th>
                <td>{{ $supplier->sub_number }}</td>
            </tr>
            <tr>
                <th>المرفقات</th>
                <td>
                    @if(!empty($supplier->attachments) && is_array($supplier->attachments))
                        <ul class="list-unstyled" id="attachmentsList">
                            @foreach($supplier->attachments as $index => $file)
                                <li class="mb-2">
                                    <a href="{{ $file }}" target="_blank">
                                        <i class="fa fa-paperclip"></i> {{ basename($file) }}
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-attachment" 
                                            data-id="{{ $supplier->id }}" 
                                            data-index="{{ $index }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">لا يوجد مرفقات</span>
                    @endif

                    <hr>
                    <!-- Form رفع مرفقات جديدة -->
                    <form id="uploadAttachmentForm" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="attachments[]" multiple class="form-control mb-2" accept=".jpg,.png,.jpeg,.pdf">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-upload"></i> رفع مرفقات
                        </button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
    // حذف مرفق
    $(document).on('click', '.delete-attachment', function(){
        var supplierId = $(this).data('id');
        var index = $(this).data('index');
        var btn = $(this);

        if(confirm('هل تريد حذف هذا المرفق؟')){
            $.ajax({
                url: '/suppliers/' + supplierId + '/attachments/' + index,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res){
                    toastr.success(res.message);
                    btn.closest('li').remove();
                },
                error: function(){
                    toastr.error('فشل الحذف');
                }
            });
        }
    });

    // رفع مرفقات جديدة
    $('#uploadAttachmentForm').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/suppliers/{{ $supplier->id }}/attachments',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.message);
                // تحديث القائمة
                $('#attachmentsList').html('');
                if(res.attachments.length > 0){
                    res.attachments.forEach((file, index) => {
                        $('#attachmentsList').append(`
                            <li class="mb-2">
                                <a href="${file}" target="_blank">
                                    <i class="fa fa-paperclip"></i> ${file.split('/').pop()}
                                </a>
                                <button class="btn btn-sm btn-danger delete-attachment" 
                                        data-id="{{ $supplier->id }}" 
                                        data-index="${index}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </li>
                        `);
                    });
                }
                $('#uploadAttachmentForm')[0].reset();
            },
            error: function(){
                toastr.error('فشل رفع الملفات');
            }
        });
    });
</script>
@endpush
