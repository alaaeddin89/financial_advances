<!-- Modal: Create Supplier -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" role="dialog" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
    <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="createSupplierModalLabel">إضافة مورد جديد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
    </div>

      <form id="createSupplierForm" method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        <div class="modal-body">

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">اسم المورد (عربي)</label>
            <div class="col-sm-9">
              <input type="text" name="name_ar" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">اسم المورد (إنجليزي)</label>
            <div class="col-sm-9">
              <input type="text" name="name_en" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الرقم الضريبي</label>
            <div class="col-sm-9">
              <input type="text" name="tax_id_no" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">السجل التجاري</label>
            <div class="col-sm-9">
              <input type="text" name="commercial_register_no" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الهاتف</label>
            <div class="col-sm-9">
              <input type="text" name="phone" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">العنوان الوطني</label>
            <div class="col-sm-9">
              <input type="text" name="national_address" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">رقم المبنى</label>
            <div class="col-sm-9">
              <input type="text" name="building_number" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">الرقم الفرعي</label>
            <div class="col-sm-9">
              <input type="text" name="sub_number" class="form-control">
            </div>
          </div>

          
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
          <button type="submit" class="btn btn-success">حفظ</button>
        </div>
      </form>

    </div>
  </div>
</div>
