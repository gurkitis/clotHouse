<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClothingUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clothing_unit', function (Blueprint $table) {
            $table->id();
            $table->string('identificator', 255)->nullable(FALSE);
            $table->foreignId('status')->nullable(FALSE)->references('id')->on('status')->cascadeOnDelete();
            $table->foreignId('clothing')->nullable(FALSE)->references('id')->on('clothing')->cascadeOnDelete();
            $table->foreignId('warehouse')->nullable(FALSE)->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreignId('organization')->nullable(FALSE)->references('id')->on('organization')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clothing_unit');
    }
}
