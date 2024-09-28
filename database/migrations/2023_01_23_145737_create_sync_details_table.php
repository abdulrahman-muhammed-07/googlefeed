<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_details', function (Blueprint $table) {
            $table->unsignedBigInteger('sync_store_id');
            $table->foreign('sync_store_id')->references('store_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->char('sync_detail_id', 32);
            $table->primary('sync_detail_id');
            $table->string('sync_type')->default('products');
            $table->integer('last_created');
            $table->integer('last_updated');
            $table->integer('last_sync');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_details');
    }
};
