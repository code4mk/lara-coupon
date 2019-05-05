<?php
namespace Code4mk\LaraCoupon;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Code4mk\LaraCoupon\Model\CouponSingleRedeem;
use Code4mk\LaraCoupon\Model\Coupon as LaraPromo;
use Illuminate\Http\Request;
use Keygen\Keygen;
use DateInterval;
use DateTime;
use Config;
use DB;
class Coupon
{
  private $status;
  private $isProduct = false;
  private $isUser = false;
  private $isSingleRedeem = false;
  private $isQunatity = false;

  public function create($authUser)
  {
    $expiredTime = Config::get('laraCoupon.expired') ? Config::get('laraCoupon.expired') : 'P7Y5M4DT4H3M2S';
    $isCodePrefix = Config::get('laraCoupon.isCodePrefix') ? Config::get('laraCoupon.isCodePrefix') : true;
    $codePrefix = Config::get('laraCoupon.codePrefix') ? Config::get('laraCoupon.codePrefix') : 'PM-';
    $codeLenght = Config::get('laraCoupon.codeLenght') ? Config::get('laraCoupon.codeLenght') : 10;
    $expiredDate = new DateTime();
    $expiredDate->add(new DateInterval($expiredTime));
    // create a coupon
    $coupon = new LaraPromo;
    if(\Request::get('code')){
      $coupon->code = \Request::get('code');
    }else{
      if($isCodePrefix){
        $coupon->code = Keygen::bytes($codeLenght)->hex()->prefix($codePrefix)->generate('strtoupper');
      }else{
        $coupon->code = Keygen::bytes($codeLenght)->hex()->generate('strtoupper');
      }
    }
    if(\Request::get('quantity')){
      $coupon->quantity = \Request::get('quantity');
      $coupon->is_quantity = true;
      $coupon->usedq = 0;
    }
    $coupon->description = \Request::get('description');
    $coupon->type = \Request::get('type');
    $coupon->amount = \Request::get('amount');
    $coupon->issuer = $authUser;
    $coupon->product_id = \Request::get('product_id');
    $coupon->user_id = \Request::get('user_id');
    $coupon->expire = $expiredDate->getTimestamp();
    $coupon->is_active = true;
    $coupon->is_used = false;
    if(\Request::get('rsingle')){
      $coupon->is_rsingle = true;
    }else{
      $coupon->is_rsingle = false;
    }
    if(\Request::get('user_id')){
      $coupon->is_user = true;
    }
    if(\Request::get('product_id')){
      $coupon->is_product = true;
    }
    $coupon->save();
  }

  public function singleRedeem($code, $authUser)
  {
    $coupon = new CouponSingleRedeem;
    $coupon->code = $code;
    $coupon->redeem_user = $authUser;
    $coupon->save();
  }

  public function singleUnredeem($code,$authUser)
  {
    $coupon =  CouponSingleRedeem::where('code',$code)
                                  ->where('redeem_user',$authUser)
                                  ->first();
    $coupon->delete();
  }

  public function redeem($code)
  {
    DB::transaction(function () use($code){
      $coupon = LaraPromo::where('code',$code)->first();
      $coupon->usedq = $coupon->usedq + 1;
      $coupon->is_used = true;
      $coupon->save();
    });
  }

  public function unredeem($code)
  {
    DB::transaction(function () use($code){
      $coupon = LaraPromo::where('code',$code)->first();
      $coupon->usedq = $coupon->usedq - 1;
      if($coupon->usedq == 0){
        $coupon->is_used = false;
      }
      $coupon->save();
    });
  }

  public function lists()
  {
    $coupons = LaraPromo::all();
    return $coupons;
  }

  public function activeLists()
  {
    $coupons = LaraPromo::where('is_active',true)
                      ->get();
    return $coupons;
  }

  public function deactiveLists()
  {
    $coupons = LaraPromo::where('is_active',false)
                      ->get();
    return $coupons;
  }

  public function general()
  {
    $coupons = LaraPromo::where('is_user',false)
                      ->where('is_product',false)
                      ->get();
    return $coupons;
  }

  public function delete($code)
  {
    $coupon = LaraPromo::where('code',$code)->first();
    $coupon->delete();
  }

  public function check($code,$authUser=0)
  {
    $instaceTime = new DateTime();
    $currentTimeAmp = $instaceTime->getTimestamp();
    // check coupon
    $coupon = LaraPromo::where('code',$code)
                    ->where('is_active',true)
                    ->where('expire','>',$currentTimeAmp)
                    ->first();
    if(!is_null($coupon)){
      // specific product's coupon
      if($coupon->is_product){
        dd("what");
        if($coupon->product_id == \Request::get('product_id')){
          // next condition check user
          $this->status = true;
          $this->isProduct = true;
        }else{
          $coupon_data = [
            'status' => false
          ];
          return $coupon_data;
        }
      }
      // specific user's coupon
      if($coupon->is_user){
        if($coupon->user_id == \Request::get('user_id')){
          // next condition check user
          $this->status = true;
          $this->isUser = true;
        }else{
          $coupon_data = [
            'status' => false
          ];
          return $coupon_data;
        }
      }
      // check coupon quantity
      if($coupon->is_quantity){
        if($coupon->usedq <= $coupon->quantity){
          // next condition check user
          $this->status = true;
          $this->isQunatity = true;
        }else{
          $coupon_data = [
            'status' => false
          ];
          return $coupon_data;
        }
      }

      if($coupon->is_rsingle){
        $singleCoupon = CouponSingleRedeem::where('redeem_user',$authUser)
                                ->where('code',$code)
                                ->first();
        if(is_null($singleCoupon)){
          // next condition check user
          $this->status = true;
          $this->isSingleRedeem = true;
        }else{
          $coupon_data = [
            'status' => false
          ];
          return $coupon_data;
        }
      }

    }else{
      $coupon_data = [
        'status' => false
      ];
      return $coupon_data;
    }
     $promoDatas = [
      "status" => true,
      "type" => $coupon->type,
      "is_product" => $this->isProduct,
      "is_user" => $this->isUser,
      "is_quantity" => $this->isQunatity,
      "is_rsingle" => $this->isSingleRedeem,
      "amount" => $coupon->amount
    ];
    return $promoDatas;
  }
}
