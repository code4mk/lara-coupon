<?php

namespace Code4mk\LaraCoupon;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Code4mk\LaraCoupon\Coupon as LaraPromo;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @since     2019
 * @copyright 0dev.co (https://0dev.co)
 */

class LaraCouponServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot()
   {
     // publish database
     $this->publishes([
       __DIR__ . '/../migrations/' => base_path('/database/migrations'),
      ], 'migrations');
     // publish config
     $this->publishes([
       __DIR__ . '/../config/laraCoupon.php' => config_path('laraCoupon.php'),
     ], 'config');

      AliasLoader::getInstance()->alias('KCoupon', 'Code4mk\LaraCoupon\Facades\Coupon');
   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
     $this->app->bind('kcoupon', function () {
      return new LaraPromo;
     });
   }
}
