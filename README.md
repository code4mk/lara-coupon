<p align="center" ><img src="https://user-images.githubusercontent.com/17185462/57058050-40a38880-6ccf-11e9-974f-8324b01f8a72.png"></p>

# lara-coupon
easily create laravel coupon code with with lara-coupon

# installation

```bash
composer require code4mk/lara-coupon
```

# setup

## 1) vendor publish

```bash
php artisan vendor:publish --provider="Code4mk\LaraCoupon\LaraCouponServiceProvider" --tag=config
php artisan vendor:publish --provider="Code4mk\LaraCoupon\LaraCouponServiceProvider" --tag=migrations
```

## 2) config

* `config/laraCoupon.php`
* setup `expired time`,`prefix`,`code length`
* `expire time ` must be follow [P7Y5M4DT4H3M2S](https://www.php.net/manual/en/datetime.add.php)

```php
"expired" => "PT12M",
"isCodePrefix" => true,
"codePrefix" => "PMM-",
"codeLenght" => 10
```

* `php artisan config:clear`

# method

## `create()`

```php
use KCoupon;
KCoupon::create($authUser)
```

* create method has more request data
* `code,quantity,type,amount,product_id,user_id,rsingle`
* if you want auto code that time don't use `code` in request query

* if coupon code will need quantity that time use request query `quantity=integer` as `?quantity=20`
* type is fix or per (percent) `?type=fix` , `?type=per`
* amount `?amount=120`, `?amount=10`
* specific product's coupon `?product_id=12`
* specific user's coupon `?user_id=2`
* redeem one user only one time `?rsingle=true`
* all products coupon for all user
  * without query `product_id,user_id`

## `singleRedeem()`

```php
use KCoupon;
KCoupon::singleRedeem($code, $authUser)
```

## `singleUnredeem()`

```php
use KCoupon;
KCoupon::singleUnredeem($code, $authUser)
```

## `redeem()`

```php
use KCoupon;
KCoupon::redeem($code)
```

## `unredeam()`

```php
use KCoupon;
KCoupon::unredeam($code)
```

## lists

```php
use KCoupon;
KCoupon::lists();
```

## lists

```php
use KCoupon;
KCoupon::activeLists();
```

## lists

```php
use KCoupon;
KCoupon::deactiveLists();
```

## general

```php
use KCoupon;
KCoupon::general();
```

## `delete`

```php
use KCoupon;
KCoupon::delete($code);
```

## `check`

```php
use KCoupon;
KCoupon::check($code,$authUser);
```

* check return

```php
"status" => ,
"type" => ,
"is_product" => ,
"is_user" => ,
"is_quantity" => ,
"is_rsingle" => ,
"amount" =>
```

<a href="https://twitter.com/0devco" target="_blank" ><p align="center" ><img src="https://raw.githubusercontent.com/0devco/docs/master/.devco-images/logo-transparent.png"></p></a>
