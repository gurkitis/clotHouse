<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Clothing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clothing', function (Blueprint $table) {
            $table->id();
            $table->binary('image')->nullable();
            $table->string('name', 255)->nullable(FALSE);
            $table->foreignId('category')->nullable(FALSE)->references('id')->on('category')->cascadeOnDelete();
            $table->foreignId('organization')->nullable(FALSE)->references('id')->on('organization')->cascadeOnDelete();
        });
    }
}
