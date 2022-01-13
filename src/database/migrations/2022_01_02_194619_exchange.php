<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Exchange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable(FALSE);
            $table->text('information')->nullable();
            $table->foreignId('clothing_unit')->nullable(FALSE)->references('id')->on('clothing_unit')->cascadeOnDelete();
            $table->foreignId('issuer_warehouse')->nullable()->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreignId('receiver_warehouse')->nullable()->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreignId('facilitator')->nullable(FALSE)->references('id')->on('user')->cascadeOnDelete();
        });
    }
}
