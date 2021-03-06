<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigitalInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_inventories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('description');
            $table->string('file');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('inventory_crossover_enabled');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digital_inventories');
    }
}
