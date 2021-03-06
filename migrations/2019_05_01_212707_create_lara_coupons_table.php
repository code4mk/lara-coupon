<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaraCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lara_coupons', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('description')->nullable();
          $table->string('code')->unique()->nullable();
          $table->string('type')->nullable();
          $table->float('amount',18,2)->nullable();
          $table->integer('issuer')->nullable();
          $table->integer('product_id')->nullable();
          $table->integer('user_id')->nullable();
          $table->integer('quantity')->nullable();
          $table->integer('usedq')->nullable();
          $table->boolean('is_user')->default(false);
          $table->boolean('is_product')->default(false);
          $table->boolean('is_active')->default(true);
          $table->boolean('is_used')->nullable();
          $table->boolean('is_quantity')->default(false)->nullable();
          $table->integer('expire')->nullable();
          $table->timestamps();
        });
        // single redeem for general coupon
        Schema::create('laracoupon_single_redeem', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('redeem_user');
            $table->string('code');
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
        Schema::dropIfExists('lara_coupons');
        Schema::dropIfExists('laracoupon_single_redeem');
    }
}
