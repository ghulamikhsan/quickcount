<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('province', 255)->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->string('city', 255)->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->string('subdistrict', 255)->nullable();
            $table->bigInteger('subdistrict_id')->nullable();
            $table->text('address');
            $table->char('used', 1)->default(0);
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
        Schema::dropIfExists('shipment');
    }
}
