<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\CartDetail;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ModelsQuery\PromoCodeModel;
use App\Models\ModelsQuery\CartModel;

class PromotionModel extends Model
{
    protected $promotion;
    protected $cartModel;
    public function __construct(PromoCodeModel $promoCodeModel, CartModel $model) {
        $this->promotion = new Promotion();
        $this->promoCodeModel = $promoCodeModel;
        $this->cartModel = $model;
    }
    public function getPromotions($request){
        $query =  Promotion::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        $query->with('details');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['show_web'])){
            $query->where('show_web', $request['show_web']);
        }
        $limit = $request['limit'] ?? 10;
        if ($limit == 1){
            return $query->first();
        }
        if ($limit > 1){
            return $query->paginate($limit);    
        }
            
    }
    public function getPromotionForCart($cart){
        // if (count($cart->details) == 0){
        //     $cart->deleted_at = Carbon::now();
        //     $cart->save();
        // }
        // $cart->gifts = [];

        // $cart->discount_price = 0;
        $info_payment[0] = [
            "total_price" => $cart->details->sum('total_price'),
            "total_price_text" => number_format($cart->details->sum('total_price'),0,',','.') . " đ",
            "name_show" => "Tổng tiền hàng"
        ];

        
        //start Promotion Discount
        if ($cart->discount_code){
            $discount = Discount::whereNull('deleted_at')->where('code', $cart->discount_code)->with('conditions')->where('start_date', '<=', Carbon::now('Asia/Ho_Chi_Minh'))->where('end_date', '>=', Carbon::now('Asia/Ho_Chi_Minh'))->first();
            $apply = $this->checkConditionDiscount($cart, $discount);
            if ($apply){
                $discount_total = 0;
                if ($discount->discount_price > 0){
                    $discount_total = $discount->discount_price;
                }
            
                if ($discount->discount_percent > 0){
                    $discount_total = $info_payment[0]['total_price'] * round(($discount->discount_percent / 100),1);
                }
                $cart->discount_code = $discount->code;
                // $cart->discount_name = $discount->name;
                $cart->discount_price = $discount_total;   
                $info_payment[count($info_payment)] = [
                    "total_price" => $discount_total,
                    "total_price_text" => "-" . number_format($discount_total,0,',','.') . " đ",
                    "name_show" => $discount->name
                ];
            }
        }


        $total_price_payment = $cart->details->sum('total_price') - $cart->discount_price;
        $cart->total_price = $total_price_payment;
        $info_payment[count($info_payment)] = [
            "total_price" => $total_price_payment,
            "total_price_text" => number_format($total_price_payment,0,',','.') . " đ",
            "name_show" => "Tổng thanh toán"
        ];
        $cart->info_payment = json_encode($info_payment);
        // $cart->gifts = json_encode($cart->gifts);
        // $cart->total_price = $cart->details->sum('total');
        // $cart->total_pay = $cart->total_price - $cart->discount_price;
        $cart->save();
       return $cart;
    }
    public function createPromotions($req){
            try {
                //code...
                DB::beginTransaction();
                    $promotion = new Promotion();
                if (!empty($req['id'])){
                    $promotion = Promotion::whereNull('deleted_at')->find($req['id']);
                }
                    $promotion->promotion_code = $req['code']??($promotion->promotion_code?$promotion->promotion_code:null);
                    $promotion->promotion_name = $req['name']??($promotion->promotion_name?$promotion->promotion_name:null);
                    $promotion->start_time = $req['start_time']?Carbon::parse($req['start_time']):$promotion->start_time;
                    $promotion->end_time = $req['end_time']?Carbon::parse($req['end_time']):$promotion->end_time;
                    
                    $promotion->status = $req['status'];
                    $promotion->show_web = $req['show_web']??0;
                    $promotion->img = $req['img']??NULL;
                    $promotion->conditions = json_encode($req['conditions'])??($promotion->conditions?$promotion->conditions:null);
                    $promotion->gifts = json_encode($req['gifts'])??($promotion->gifts?$promotion->gifts:null);
                    $promotion->condition_apply = $req['condition_apply']??($promotion->condition_apply?$promotion->condition_apply:"TOGETHER");
                    $promotion->save();
                DB::commit();
                return $promotion;
            } catch (Exception $e) {
                DB::rollBack();
               dd($e);
            }
    }
    public function checkConditionDiscount($cart, $discount){
        $apply = false;
        if ($discount){
           
            if ($discount->condition_apply == "ALL"){
                foreach ($discount->conditions as $condition){
                if ($condition->type == "cart"){  
                    if (!comparative($cart->details->sum('total_price'), $condition->condition_data->condition,$condition->condition_data->value)){
                        $apply = true;
                        break;
                    }
                } 
                if ($condition->type == "theme_id"){  
                    if (!in_array($condition->condition_data->value, array_column($cart->details, 'theme_id'))){
                        $apply = true;
                        break;
                    }
                } 
            }
            }else{
                foreach ($discount->conditions as $condition){
                    
                    if ($condition->condition_apply== "cart"){  
                        if ($this->comparative($cart->details->sum('total_price'), $condition->condition_data->condition,$condition->condition_data->value)){
                            $apply = true;
                            break;
                        }
                    } 
                    if ($condition->condition_apply == "theme_id"){  
                        if (in_array($condition->condition_data->value, array_column($cart->details->toArray(), 'theme_id'))){
                            $apply = true;
                            break;
                        }
                    } 
                }
            }

            
        }else{
            $cart->discount_code = null;
            // $cart->discount_name = null;
            $cart->discount_price = 0;
        }
                return $apply;
            }
    public function comparative($value, $method, $value2){
            if ($method == "<"){
                return $value < $value2;
            }
            if ($method == "<="){
                return $value <= $value2;
            }
            if ($method == "="){
                return $value == $value2;
            }
            if ($method == ">"){
                return $value > $value2;
            }
            if ($method == ">="){
                return $value >= $value2;
            }
    }
}