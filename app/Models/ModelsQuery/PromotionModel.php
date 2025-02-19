<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\CartDetail;
use App\Models\ProductFillter;
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
        if (count($cart->details) == 0){
            $cart->deleted_at = Carbon::now();
            $cart->save();
        }
        $cart->gifts = [];

        $cart->discount_price = 0;
        $info_payment[0] = [
            "total_price" => $cart->details->sum('total') - $cart->discount_price,
            "total_price_text" => number_format($cart->details->sum('total') - $cart->discount_price,0,',','.') . " đ",
            "name_show" => "Tổng tiền hàng"
        ];

        $promotions = Promotion::whereNull('deleted_at')->where('start_time','<=',Carbon::now())->where('end_time','>=',Carbon::now())->where('status',1)->get();
        if (!empty($promotions)){
                foreach($promotions as $promotion){
                    //dieu kien
                    $next = false;
                    if (!empty($promotion->conditions)){
                        $conditions = json_decode($promotion->conditions);
                        if (count($conditions) <= 1){
                            $promotion->condition_apply = "ANY";
                        }
                        foreach($conditions as $condition){
                            if (($condition->type) == "PRODUCT_ON_CART"){
                                if ($promotion->condition_apply == "ANY"){
                                    foreach($condition->condition_data as $data){
                                        foreach($cart->details as $detail){
                                             if ($data->condition_type == "price"){
                                                 
                                                 if ($detail->product->code == $data->product_code && $this->comparative($detail->product->price,$data->condition,$data->number)){
                                                     $next = true;
                                                     break;
                                                 }   
                                             }
                                             if ($data->condition_type == "number"){
                                                 if ($detail->product->code == $data->product_code && $this->comparative($detail->qty, $data->condition,$data->number)){
                                                     $next = true;
                                                     break;
                                                 }  
                                             }
                                             
                                        } 
                                    }
                                }
                                if ($promotion->condition_apply == "TOGETHER"){
                                    $found = false;
                                    foreach($condition->condition_data as $data){
                                        $found = collect($cart->details)->contains(function ($item) use ($data) {
                                            if ($data->condition_type == "number"){
                                                return $item->product->code == $data->product_code && $this->comparative($data->number,$data->condition,$item->qty);
                                            }
                                            if ($data->condition_type == "price"){
                                                return $item->product->code == $data->product_code && $this->comparative($data->number,$data->condition,$item->price);
                                            }
                                        }); 
                                        if ($found == false){
                                            break;
                                        }
                                    }
                                    if ($found){
                                        $next = true;
                                    }
                                }
                            }
                        }
                    }
                    if($next == false){
                        continue;
                    }
                    
                    $gifts = json_decode($promotion->gifts);
                    if (!empty($gifts)){
                        foreach($gifts as $item){
                            if ($item->type == "DIRECT_GIFT"){ //qua tang truc tiep
                                    foreach($item->gifts as $gift){
                                        $product_gift = Product::whereNull('deleted_at')->where('code',$gift->product_code)->select('id','code','name','image_url')->first();
                                        
                                        $scope_product_qty_cart = 0;
                                        foreach($cart->details as $d){
                                            if ($d->product_id == ($product_gift->id ?? 0)){
                                                $scope_product_qty_cart = $d->qty;
                                            }
                                        }
                                        // dd($this->cartModel->getWarehouseNear($cart->lat, $cart->lon, $product_gift->id ?? 0)['data']);
                                        // dd($this->cartModel->getWarehouseNear($cart->lat, $cart->lon, $product_gift->id ?? 0)['data']['qty'],($gift->qty + $scope_product_qty_cart));
                                        if (!empty($product_gift) && $this->cartModel->getWarehouseNear($cart->lat, $cart->lon, $product_gift->id ?? 0)['data']['qty'] >= ($gift->qty + $scope_product_qty_cart)) {
                                            $cart->gifts = array_merge($cart->gifts ?? [], [
                                                [   "promotion_code" => $promotion->promotion_name,
                                                    "promotion_name" => $promotion->promotion_name,
                                                    "product_code" => $product_gift->code,
                                                    "product_name" => $product_gift->name,
                                                    "product_image" => $product_gift->image_url,
                                                    "qty" => $gift->qty
                                                ]
                                            ]);    
                                        }
                                    }
                            }
                            if ($item->type == "DISCOUNT_PRICE"){ //giảm giá sản phẩm
                                foreach($item->gifts as $gift){
                                    if ($gift->type_discount == "percent"){
                                        // $product_gift = Product::whereNull('deleted_at')->where('code',$gift->product_code)->select('code','price','name','image_url')->first();

                                            foreach($cart->details as $detail){
                                                if ($detail->product->code == $gift->product_code){
                                                    
                                                    $detail->discount_price = 0;
                                                    $detail->discount_code= "";
                                                    $detail->discount_name = "";

                                                    $detail->discount_code = $promotion->promotion_code;
                                                    $detail->discount_name = $promotion->promotion_name;

                                                    $discount = (($detail->product->price * $detail->qty) * $gift->value) / 100;
                                                    
                                                    

                                                    if ($discount > ($detail->product->price * $detail->qty)){
                                                        $discount = ($detail->product->price * $detail->qty);
                                                    }
                                                    
                                                    $detail->discount_price = $discount;
                                                    $detail->total = ($detail->product->price * $detail->qty) - $discount;
                                                    $detail->total_text = number_format($detail->total,0,',','.') . " đ";
                                                    $detail->total_old_text = number_format(($detail->product->price * $detail->qty),0,',','.') . " đ";

                                                    $info_payment[] = [
                                                        "total_price" => $discount,
                                                        "total_price_text" => "-" . number_format($discount,0,',','.') . " đ",
                                                        "name_show" => $promotion->promotion_name
                                                    ];
                                                    if($detail->product_id == 25){
                                                        // dd($detail->id, $detail->discount_price);
                                                    }
                                                    $detail->save();
                                                    $cart->discount_price += $discount;
                                                    break;
                                                }
                                            }
                                    }

                                    if ($gift->type_discount == "price"){
                                        // $product_gift = Product::whereNull('deleted_at')->where('code',$gift->product_code)->select('code','price','name','image_url')->first();

                                            foreach($cart->details as $detail){
                                                if ($detail->product->code == $gift->product_code){
                                                    
                                                    $detail->discount_price = 0;
                                                    $detail->discount_code= "";
                                                    $detail->discount_name = "";

                                                    $detail->discount_code = $promotion->promotion_code;
                                                    $detail->discount_name = $promotion->promotion_name;

                                                    $discount = (($detail->product->price * $detail->qty) - $gift->value);
                                                    
                                                    

                                                    if ($discount > ($detail->product->price * $detail->qty)){
                                                        $discount = ($detail->product->price * $detail->qty);
                                                    }

                                                    $detail->discount_price = $discount;
                                                    $detail->total = ($detail->product->price * $detail->qty) - $discount;

                                                    $detail->total_text = number_format($detail->total,0,',','.') . " đ";
                                                    $detail->total_old_text = number_format(($detail->product->price * $detail->qty),0,',','.') . " đ";
                                                    $cart->discount_price += $discount;

                                                    $info_payment[] = [
                                                        "total_price" => $discount,
                                                        "total_price_text" => "-" . number_format($discount,0,',','.') . " đ",
                                                        "name_show" => $promotion->promotion_name
                                                    ];
                                                    
                                                    $detail->save();
                                                    break;
                                                }
                                            }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        


            // $cart->details()->saveMany($cart->details);



        $cart->info_payment = json_encode($info_payment);
        $cart = $this->promoCodeModel->applyPromoCode($cart); //use coupon


        $info_payment = json_decode($cart->info_payment);

        
        $total_price_payment = $cart->details->sum('total') - $cart->discount_price;
        
        $info_payment[count($info_payment)] = [
            "total_price" => $total_price_payment,
            "total_price_text" => number_format($total_price_payment,0,',','.') . " đ",
            "name_show" => "Tổng thanh toán"
        ];
        $cart->info_payment = json_encode($info_payment);
        $cart->gifts = json_encode($cart->gifts);
        $cart->total_price = $cart->details->sum('total');
        $cart->total_pay = $cart->total_price - $cart->discount_price;
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
