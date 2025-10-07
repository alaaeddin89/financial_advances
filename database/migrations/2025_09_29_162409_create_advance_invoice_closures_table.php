<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceInvoiceClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_invoice_closures', function (Blueprint $table) {
            $table->id();

            // روابط الجداول
            $table->foreignId('advance_id')->constrained('financial_advances')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('expense_invoices')->onDelete('cascade');
            
            // قيمة التقفيل
            $table->decimal('closed_amount', 10, 2)->comment('قيمة المبلغ المقفل من العهدة بهذه الفاتورة');
            
            // تفاصيل العملية
            $table->timestamp('closure_date')->useCurrent();
            $table->foreignId('closed_by_user_id')->constrained('users')->comment('المحاسب الذي قام بإجراء التقفيل');

            // المرحلة 5: اعتماد المحاسب
            $table->boolean('accountant_approved')->default(false);
            $table->dateTime('accountant_approval_date')->nullable();

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
        Schema::dropIfExists('advance_invoice_closures');
    }
}
