<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Session extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 255)->nullable(FALSE)->unique();
            $table->dateTime('last_request_at')->nullable(FALSE);
            $table->foreignId('user')->nullable(FALSE)->references('id')->on('user')->cascadeOnDelete();
        });
    }
}
