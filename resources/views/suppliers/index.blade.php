@extends('layouts.app_admin')
@section('title','كشف الموردين')
@section('toolbar.title','لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">لوحة التحكم</li>
@endsection
@push('css')
    <link href="{{asset('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            @yield("title")
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-table-toolbar="base">
                <button class="btn btn-success" data-toggle="modal" data-target="#createSupplierModal">
                    <i class="fa fa-plus"></i> مورد جديد
                </button>

            </div>
        </div>
    </div>

    <div class="card-body py-3">
        <div class="table-responsive">
            <table id="suppliers_table" class="table table-bordered table-hover text-center fs-7">
                <thead>
                <tr class="fw-bolder bg-secondary text-muted">
                    <th>#</th>
                    <th>اسم المورد</th>
                    <th>الرصيد</th>
                    <th>الموظف</th>
                    <th>الهاتف</th>
                    <th>العنوان</th>
                    <th>الإجراءات</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@include("modals.supplier")

<!-- Modal: Edit Supplier -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editSupplierModalLabel">تعديل بيانات المورد</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="إغلاق">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="editSupplierForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          
          <input type="hidden" name="id" id="edit_supplier_id">

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">اسم المورد (عربي)</label>
            <div class="col-sm-9">
              <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">اسم المورد (إنجليزي)</label>
            <div class="col-sm-9">
              <input type="text" name="name_en" id="edit_name_en" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الرقم الضريبي</label>
            <div class="col-sm-9">
              <input type="text" name="tax_id_no" id="edit_tax_id_no" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">السجل التجاري</label>
            <div class="col-sm-9">
              <input type="text" name="commercial_register_no" id="edit_commercial_register_no" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الهاتف</label>
            <div class="col-sm-9">
              <input type="text" name="phone" id="edit_phone" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">العنوان الوطني</label>
            <div class="col-sm-9">
              <input type="text" name="national_address" id="edit_national_address" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">رقم المبنى</label>
            <div class="col-sm-9">
              <input type="text" name="building_number" id="edit_building_number" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الرقم الفرعي</label>
            <div class="col-sm-9">
              <input type="text" name="sub_number" id="edit_sub_number" class="form-control">
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
          <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        </div>
      </form>

    </div>
  </div>
</div>


@endsection

@push('js')
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script>
        $(function () {
            $('#suppliers_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('suppliers.index') }}",
                language: {
                    "lengthMenu": "عرض _MENU_ صف في الصفحة",
                    "zeroRecords": "لم يتم إيجاد شيء",
                    "info": "عرض صفحة _PAGE_ من _PAGES_",
                    "infoEmpty": "لا يوجد أي بيانات متاحة",
                    "infoFiltered": "(تصفية من _MAX_ العدد الكلي للصفوف)",
                    "sSearch": "البحث:"
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'balance', name: 'balance'},
                    {data: 'contact_person', name: 'contact_person'},
                    {data: 'phone', name: 'phone'},
                    {data: 'address', name: 'address'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });

        function deleteRecord(id) {
            if (confirm('هل أنت متأكد من الحذف؟')) {
                $.ajax({
                    url: "{{ route('suppliers.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    // CRITICAL: Make sure the CSRF token is correctly passed in the data object for DELETE requests
                    data: { 
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        // 1. حالة النجاح (Success) - المورد حُذف
                        if (res.success) {
                            // رسالة افتراضية للنجاح أو يمكنك استخدام res.message إذا أضفتها
                            alert('تم حذف المورد بنجاح.'); 
                            // إعادة تحميل DataTables لإزالة الصف المحذوف
                            $('#suppliers_table').DataTable().ajax.reload();
                        } else {
                            // 2. حالة الفشل المنطقي (Logical Failure) - يحدث إذا كان رمز الحالة 200 ولكن success: false 
                            // (وهذا لا ينبغي أن يحدث في الكود الحالي، لكنه للحماية)
                            alert(res.message || 'فشل في عملية الحذف.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // 3. حالة الخطأ HTTP (مثل 403, 409, 500)
                        let message = 'حدث خطأ غير معروف أثناء الحذف.';
                        
                        try {
                            // محاولة تحليل استجابة الخادم كـ JSON
                            const response = JSON.parse(xhr.responseText);

                            // إذا كان الخادم قد أرجع رسالة في حقل 'message'
                            if (response && response.message) {
                                message = response.message;
                            }

                        } catch (e) {
                            // إذا فشل التحليل، نستخدم رسالة الخطأ الافتراضية
                            if (xhr.status === 409) {
                                message = 'لا يمكن حذف المورد لوجود فواتير مرتبطة به.';
                            } else if (xhr.status === 403) {
                                message = 'لا توجد لديك صلاحيات لإجراء هذا الحذف.';
                            }
                        }
                        
                        alert(message);
                    }
                });
            }
        }

        $('#createSupplierForm').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res){
                    $('#createSupplierModal').modal('hide');
                    $('#createSupplierForm')[0].reset();
                    $('#suppliers_table').DataTable().ajax.reload();
                    toastr.success('تم إضافة المورد بنجاح');
                },
                error: function(xhr){
                    toastr.error('حدث خطأ أثناء الإضافة');
                }
            });
        });


        // عند الضغط على زر تعديل
        $(document).on('click', '.editSupplier', function(){
            var id = $(this).data('id');
            $.get("/suppliers/" + id + "/edit", function(supplier){
                $('#edit_supplier_id').val(supplier.id);
                $('#edit_name_ar').val(supplier.name_ar);
                $('#edit_name_en').val(supplier.name_en);
                $('#edit_tax_id_no').val(supplier.tax_id_no);
                $('#edit_commercial_register_no').val(supplier.commercial_register_no);
                $('#edit_phone').val(supplier.phone);
                $('#edit_national_address').val(supplier.national_address);
                $('#edit_building_number').val(supplier.building_number);
                $('#edit_sub_number').val(supplier.sub_number);

                $('#editSupplierForm').attr('action', '/suppliers/' + supplier.id);
                $('#editSupplierModal').modal('show');
            });
        });

        // عند الحفظ
        $('#editSupplierForm').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'PUT', // مباشرة PUT
                data: $(this).serialize(),
                success: function(res){
                    $('#editSupplierModal').modal('hide');
                    $('#editSupplierForm')[0].reset();
                    $('#suppliers_table').DataTable().ajax.reload();
                    toastr.success('تم تحديث بيانات المورد بنجاح');
                },
                error: function(xhr){
                    toastr.error('فشل تحديث المورد');
                    console.log(xhr.responseText);
                }
            });
        });

    </script>
@endpush
