<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Drop extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange');
        Schema::dropIfExists('clothing_unit');
        Schema::dropIfExists('status');
        Schema::dropIfExists('clothing');
        Schema::dropIfExists('category');
        Schema::dropIfExists('session');
        Schema::dropIfExists('organization_user');
        Schema::dropIfExists('organization_warehouse');
        Schema::dropIfExists('user');
        Schema::dropIfExists('warehouse');
        Schema::dropIfExists('organization');
    }
}
