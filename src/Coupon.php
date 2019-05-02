<?php
namespace Code4mk\LaraCoupon;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Code4mk\LaraCoupon\Model\Coupon as LaraPromo;
use Illuminate\Http\Request;
use Keygen\Keygen;
use DateInterval;
use DateTime;

class Coupon
{
  private $status;
  private $isProduct = false;
  private $isUser = false;
  private $codePrefix = true;

  public function create($authUser)
  {
    $expiredDate = new DateTime();
    $expiredDate->add(new DateInterval("PT12M"));
    // create a coupon
    $coupon = new LaraPromo;
    if(\Request::get('code')){
      $coupon->code = \Request::get('code');
    }else{
      if($this->codePrefix){
        $coupon->code = Keygen::bytes(10)->hex()->prefix('PM-')->generate('strtoupper');
      }else{
        $coupon->code = Keygen::bytes(10)->hex()->generate('');
      }
    }
    $coupon->type = \Request::get('type');
    $coupon->amount = \Request::get('amount');
    $coupon->issuer = $authUser;
    $coupon->product_id = \Request::get('product_id');
    $coupon->user_id = \Request::get('user_id');
    $coupon->expire = $expiredDate->getTimestamp();
    $coupon->is_active = true;
    if(\Request::get('user_id')){
      $coupon->is_user = true;
    }
    if(\Request::get('product_id')){
      $coupon->is_product = true;
    }
    $coupon->save();
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
      "amount" => $coupon->amount
    ];
    return $promoDatas;
  }
}
