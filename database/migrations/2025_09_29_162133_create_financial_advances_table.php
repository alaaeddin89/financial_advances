<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_advances', function (Blueprint $table) {
            $table->id();
            // الموظف المستلم للعهدة
            $table->foreignId('user_id')->constrained()->comment('الموظف المستلم للعهدة'); 
            // المحاسب الذي سجل العهدة
            $table->foreignId('issued_by_user_id')->constrained('users')->comment('المحاسب الذي سجل العهدة'); 
            
            // البيانات المالية الأساسية
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            
            // حالات العهدة والتواريخ
            $table->timestamp('issue_date')->useCurrent();
            $table->enum('status', ['Pending', 'Confirmed', 'Partially Closed', 'Closed'])->default('Pending');
            $table->timestamp('confirmation_date')->nullable()->comment('تاريخ تأكيد الموظف');

            // حقول التقفيل والترصيد الجديدة
            $table->decimal('closed_amount', 10, 2)->default(0.00)->comment('إجمالي المبلغ المقفل بالفواتير');
            $table->decimal('remaining_balance', 10, 2)->nullable()->comment('المبلغ المتبقي المستحق على الموظف');
            
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
        Schema::dropIfExists('financial_advances');
    }
}
