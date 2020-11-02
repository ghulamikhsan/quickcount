<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('privilege_id')->constrained();
            $table->foreignId('menu_id')->constrained();
            $table->enum('browse', [0,1])->default(0);
            $table->enum('read', [0,1])->default(0);
            $table->enum('edit', [0,1])->default(0);
            $table->enum('add', [0,1])->default(0);
            $table->enum('delete', [0,1])->default(0);
            $table->enum('trash', [0,1])->default(0);
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
        Schema::dropIfExists('permissions');
    }
}
