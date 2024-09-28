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
        Schema::create('google_settings', function (Blueprint $table) {
            $table->string('google_id', 500);
            $table->unsignedBigInteger('user_store_id');
            $table->boolean('google_logged_in')->default(false)->nullable();
            $table->boolean('saved_init_settings')->default(false)->nullable();
            $table->boolean('sync_status')->default(false)->nullable();
            $table->primary('google_id');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->integer('expiry_date')->nullable();
            $table->foreign('user_store_id')
                ->references('store_id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_settings');
    }
};
