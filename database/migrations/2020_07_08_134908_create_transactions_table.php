<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->char('code', 14);
            $table->float('total');
            $table->string('code_voucher', 100)->nullable();
            $table->decimal('discount_percent')->nullable();
            $table->double('discount_price')->nullable();
            $table->enum('status', ['selesai', 'proses', 'dibatalkan', 'dikirimkan']);
            $table->double('grand_total');
            $table->foreignId('created_by')->constrained('users');
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
        Schema::dropIfExists('transactions');
    }
}
