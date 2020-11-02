<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id')->nullable();
            $table->string('type', 10)->nullable();
            $table->string('province', 255)->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->string('city', 255)->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->string('subdistrict', 255)->nullable();
            $table->bigInteger('subdistrict_id')->nullable();
            $table->text('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions_shipments');
    }
}
