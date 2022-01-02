<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrganizationWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_warehouse', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('organization_warehouse');
    }
}
