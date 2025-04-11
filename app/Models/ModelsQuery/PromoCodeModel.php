<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PromoCode;
use App\Models\Cart;
use Illuminate\Support\Arr;

class PromoCodeModel extends Model
{
    public function getPromoCode($request){
        $query =  PromoCode::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['start_date'])){
            $query->where('start_date','<=', $request['start_date']);
        }
        if (!empty($request['end_date'])){
            $query->where('end_date','>=',$request['end_date']);
        }
        if (!empty($request['code'])){
            $query->where('end_date','>=',Carbon::now());
        }
        if (!empty($request['name'])){
            $query->where('name','like', "%" . $request['name'] . "%");
        }
        if (!empty($request['status'])){
            $query->where('status', $request['status']);
        }

        
            return $query->get();
    }

    public function addPromoCode($req){
        $cart = Cart::whereNull('deleted_at')->with("details")->find($req['id']);
        $cart->discount_code = "";
        for($i = 0; $i < count($req['promo_codes']); $i++){
            $cart->discount_code =  $cart->discount_code . $req['promo_codes'][$i] . ",";
        }

        
        // $cart->discount_code = SomeClass::removeLastWord($cart->discount_code);

        
        $cart->discount_code = (substr($cart->discount_code,0,strlen($cart->discount_code) - 1));
        $cart->save();
    }
    public function applyPromoCode($cart){
        $promocodes = PromoCode::whereNull('deleted_at')
        ->where('start_date', '<=', Carbon::now())
         ->where('end_date', '>=', Carbon::now())
        ->whereIn('code',explode(',', $cart->discount_code))->get();
        $info_payment = json_decode($cart->info_payment);
    
        if (!empty($promocodes)){
            
            foreach($promocodes as $key => $item){
                    $next = false;

                    // conditions
                    $item->condition_data = json_decode($item->condition_data);
                    if ($item->condition_data){
                        if ($item->condition_data[0]->all_condition_apply == "ANY"){
                            foreach($item->condition_data as $condition){
                                if ($condition->type_condition == "product"){
                                    if ($condition->condition_apply == "price"){
                                        $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                            return $detail->product->code === $condition->product_code && $this->comparative($detail->total,$condition->condition,$condition->number_product);
                                        });
                                  
                                        
                                    }
                                    if ($condition->condition_apply == "number"){
                                        foreach($cart->details as $detail){
                                            if ($detail->product->code == $condition->product_code && $this->comparative($detail->qty,$condition->condition,$condition->number_product)){
                                                $next = true;
                                                break;
                                            } 
                                        }        
                                    }
                                }
                                if ($condition->type_condition == "cart"){
                                    if ($this->comparative($cart->details->sum('total'), $condition->condition, $condition->number_cart)){
                                        $next = true;
                                    }
                                }
                                
                                if ($next == true){
                                    break;
                                }
                            }
                        }
                       
                        if ($item->condition_data[0]->all_condition_apply == "TOGETHER"){
                            $next = true; //set = true khi ko thoa man = false
                            foreach($item->condition_data as $condition){
                                if ($condition->type_condition == "product"){
                                    if ($condition->condition_apply == "price"){
                                        $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                            return $detail->product->code === $condition->product_code &&  comparative($detail->total, $condition->condition, $condition->number_product);
                                        });
                                        if ($found == null){
                                            $next = false;
                                        }
                                    }
                                    if ($condition->condition_apply == "number"){
                                        $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                            return $detail->product->code === $condition->product_code &&  comparative($detail->qty, $condition->condition, $condition->number_product);
                                        });
                                        if ($found == null){
                                            $next = false;
                                        }
                                    }
                                }
                                if ($condition->type_condition == "cart"){
                                        if (!comparative($cart->details->sum('total'), $condition->condition, $condition->number_cart)){
                                            $next = false;
                                        }
                                }
                                    

                                if ($next == false){
                                    break;
                                }
                                    
                            }
                        }
                        
                    }
       
                    //da thong qua
                    $item->discount = json_decode($item->discount);
                        if ($next == true && $item->discount){
                            // dd($item->discount);
                            foreach($item->discount as $discount){
                                if ($discount->type_apply == "product"){
                                    if ($discount->discount_type == "price"){
                                         foreach($cart->details as $detail){
                                                if($detail->product->code == $discount->product_code){
                                                    $detail->discount_price = $discount->number_product;
                                                    $detail->total = ($detail->price * $detail->qty) - $discount->number_product;
                                                    if ($discount->number_product > ($detail->price * $detail->qty) ){
                                                        $detail->discount_price = ($detail->price * $detail->qty);
                                                        $detail->total = 0;
                                                    }
                                                    if ($detail->total < 0){
                                                        $detail->total = 0;
                                                    }
                                                    $detail->save();
                                                    $info_payment[count($info_payment)] = [
                                                        "total_price" => $detail->discount_price,
                                                        "total_price_text" => "-".number_format($detail->discount_price,0,',','.') . " đ",
                                                        "name_show" => $item->name
                                                    ];
                                                    break;
                                                }
                                         }
                                    }
                                    if ($discount->discount_type == "percent"){
                                        foreach($cart->details as $detail){
                                            if($detail->product->code == $discount->product_code){

                                                $detail->discount_price = (($detail->price * $detail->qty) * $discount->number_product)/100;
                                                $detail->total = ($detail->price * $detail->qty) - $detail->discount_price ;
                                                if ($detail->discount_price > ($detail->price * $detail->qty) ){
                                                    $detail->discount_price = ($detail->price * $detail->qty);
                                                    $detail->total = 0;
                                                }
                                                if ($detail->total < 0){
                                                    $detail->total = 0;
                                                }
                                                $detail->save();
                                                $info_payment[count($info_payment)] = [
                                                    "total_price" => $detail->discount_price,
                                                    "total_price_text" => number_format($detail->discount_price,0,',','.') . " đ",
                                                    "name_show" => $item->name
                                                ];
                                                break;
                                            }
                                     }
                                   }
                                }
                                if ($discount->type_apply == "cart"){
                                        $total_price = $cart->details->sum('total');

                                        if ($discount->number_cart >= $total_price){
                                            $cart->discount_price += $total_price;

                                            $info_payment[count($info_payment)] = [
                                                "total_price" => $total_price,
                                                "total_price_text" => number_format($total_price,0,',','.') . " đ",
                                                "name_show" => $item->name
                                            ]; 
                                        }
                                        if ($discount->number_cart < $total_price){
                                            $cart->discount_price += $discount->number_cart;

                                            $info_payment[count($info_payment)] = [
                                                "total_price" => $discount->number_cart,
                                                "total_price_text" => number_format($discount->number_cart,0,',','.') . " đ",
                                                "name_show" => $item->name
                                            ]; 
                                        }
                                    
                                }
                            }
                        }

            }
        }
        $cart->info_payment = json_encode($info_payment);
        return $cart;
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
