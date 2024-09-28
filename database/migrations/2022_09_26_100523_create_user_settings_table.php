<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'user_settings',
            function (Blueprint $table) {
                $table->unsignedBigInteger('user_store_id', false);
                $table->primary('user_store_id');
                $table->bigInteger('merchant_id');
                $table->text('rule_query')->nullable();
                $table->json('mapping_settings_defaults')->nullable();
                $table->json('mapping_settings_properties')->nullable();
                $table->json('mapping_settings_selected')->nullable();
                $table->string('currency')->nullable()->default('USD');
                $table->string('service')->nullable();
                $table->string('region')->nullable();
                $table->json('over_sized_products_options')->nullable();
                $table->json('over_sized_products_options_default')->nullable();
                $table->integer('shipping_value')->nullable()->default(0);
                $table->json('smtp')->nullable();
                $table->foreign('user_store_id')->references('store_id')->on('users')->onDelete('cascade');
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
