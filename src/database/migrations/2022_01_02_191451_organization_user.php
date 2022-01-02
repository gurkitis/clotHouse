<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrganizationUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->nullable(FALSE);
            $table->boolean('is_owner')->nullable(FALSE);
            $table->foreignId('user')->nullable(FALSE)->references('id')->on('user')->cascadeOnDelete();
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
        Schema::dropIfExists('organization_user');
    }
}
