<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSerialGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\ProductSerial::query()->delete();

        Schema::create('product_serial_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('wholesaler');
            $table->string('invoice_number');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::table('product_serials', function (Blueprint $table) {
            $table->foreignId('product_serial_group_id')->constrained('product_serial_groups');
            $table->dropConstrainedForeignId('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_serials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_serial_group_id');
            $table->foreignId('user_id')->constrained('users');
        });

        Schema::dropIfExists('product_serial_groups');
    }
}
