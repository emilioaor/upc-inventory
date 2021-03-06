<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('digital_inventory_id')->constrained('digital_inventories');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['digital', 'physical']);
            $table->enum('scan_method', ['units', 'boxes'])->nullable();
            $table->integer('qty_per_box')->nullable();
            $table->integer('boxes_qty')->nullable();
            $table->integer('qty');
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
        Schema::dropIfExists('inventory_movements');
    }
}
