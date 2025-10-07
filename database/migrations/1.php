<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesReferenceIdToProSaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pro_sale_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_reference_id')->nullable()->after('user_id'); // أو بعد أي عمود تفضله
            $table->foreign('sales_reference_id')->references('id')->on('pro_sales_references')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pro_sale_invoices', function (Blueprint $table) {
            //
        });
    }
}
