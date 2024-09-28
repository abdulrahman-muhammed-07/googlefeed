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
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('user_store_id');
            $table->foreign('user_store_id')
                ->references('store_id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('product_name');
            $table->string('product_id');
            $table->string('variant_id');
            $table->string('batch_id');
            $table->primary('variant_id');
            $table->text('offer_id');
            $table->string('variant_option')->nullable();
            $table->json('google_error_array')->nullable();
            $table->text('product_image')->nullable();
            $table->enum('status', ['error', 'success', 'sent', 'pending'])->nullable();
            $table->json('response')->nullable();
            $table->boolean('is_excluded')->default(0)->nullable();
            $table->index(['user_store_id']);
            $table->index(['is_excluded']);
            $table->index(['user_store_id', 'product_id']);
            $table->index(['user_store_id', 'product_id', 'variant_id']);
            $table->index(['created_at', 'updated_at']);
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
        Schema::dropIfExists('product_logs');
    }
};
