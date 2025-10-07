<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_invoices', function (Blueprint $table) {
            $table->id();
            
            // الموظف مقدم الفاتورة
            $table->foreignId('user_id')->constrained()->comment('الموظف مقدم الفاتورة');
            // المورد (اختياري)
            $table->foreignId('supplier_id')->nullable()->constrained()->comment('المورد المرتبط بالفاتورة'); 
            
            // البيانات المالية
            $table->decimal('amount', 10, 2);
            $table->decimal('used_amount', 10, 2)->default(0.00)->comment('المبلغ المستخدم للتقفيل');
            
            // حالة الفاتورة
            $table->enum('status', ['Pending Review', 'Approved', 'Rejected'])->default('Pending Review');
            
            // المستندات والوصف
            $table->string('file_path')->comment('مسار حفظ صورة/ملف الفاتورة');
            $table->date('invoice_date');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_invoices');
    }
}
