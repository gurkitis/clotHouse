<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(FALSE);
            $table->string('surname', 255)->nullable(FALSE);
            $table->string('email', 255)->nullable(FALSE)->unique();
            $table->string('password', 256)->nullable(FALSE);
            $table->foreignId('warehouse')->nullable(FALSE)->references('id')->on('warehouse')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
