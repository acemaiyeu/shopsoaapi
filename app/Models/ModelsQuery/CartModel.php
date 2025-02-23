<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\ProductFillter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Http;

class CartModel extends Model
{
    protected $cart;
    public function __construct() {
        $this->cart = new Cart();
    }
    public function getCart($request){
        $query =  Cart::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (empty(auth()->user())){
            $query->where('session', $request['session_id']);
        }
        if (!empty(auth()->user())){
            $query->where('user_id', auth()->user()->id);
        }
        if (empty($request['detailsSort'])){
             $query->with('details');
        }
        if (!empty($request['detailsSort'])){
            $query->with('detailsHaveProductSort');
        }
          
            $query->with('user');
            return $query->first();
    }
    public function addToCart($request){
        try{
            DB::beginTransaction();
            if (!empty($request['session_id'])){
                $cart = $this->getCart($request);
                if (empty($cart)){
                    $cart  = new Cart();
                    $cart->session = $request['session_id'];
                    $cart->payment = $request['payment']??"COD";

                    if (!empty(auth()->user())){
                        $cart->user_id = auth()->user()->id;
                        $cart->phone_number = auth()->user()->phone;
                        $cart->user_name = auth()->user()->username;
                    }
                }
            }
            if (!empty($cart->warehouse_id)){
                

                $check_product = Warehouse::whereNull('deleted_at')->whereHas('details', function($query) use($request){
                    $query->where('product_id', $request['product_id'])->whereHas('product', function($query) use($request){
                        $query->where('qty','>=', $request['qty']);
                    });
                })->where('id',$cart->warehouse_id)->first();
                if (!$check_product){
                    $product_ids = !empty($cart->details)?$cart->details->pluck('product.id'):[];
                    
                    $product_ids[] = $request['product_id'];
                    // dd($product_ids);
                    $check =  DB::select("
                    SELECT count(wd.warehouse_id) FROM  warehouses w 
                    join warehouse_details wd ON w.id = wd.warehouse_id 
                    WHERE product_id IN (?) 
                    AND  w.deleted_at IS NULL 
                    AND  wd.deleted_at IS NULL 
                    GROUP BY  wd.warehouse_id 
                    HAVING COUNT(wd.warehouse_id) = ?
   
                   
               ", [$product_ids, count($product_ids)]);
                   if (count($check) != count($product_ids)){
                        return ["data" => ["message" => "Không tìm thấy kho có tất cả sản phẩm của bạn"]];
                   }else{
                        $cart->warehouse_id = $check->id;
                   }
                    
                }
            }
            // $cart->user_id = !empty($cart->user_id)?$cart->user_id:(auth()->user->id??null);
            $cart->payment = $cart->payment??null;
            $cart->discount_code = $cart->discount_code??null;
            $cart->discount_price = $cart->discount_price??0;
            $cart->note = $cart->note??null;
            $cart->promo_code = $cart->promo_code??null;
            $cart->total_price = 0;
            $cart->total_pay = 0;
            $cart->save();

            $details = CartDetail::whereNull('deleted_at')->where('cart_id', $cart->id)->get();
            if (empty($details)){
                $details = new CartDetail();
                $details->product_id = $request['product_id'];
                $details->qty = $request['qty'];
                $details->price = $request['price'];
                $details->total = $details->qty * $details->price;
                $details->save();
            } 
            if (!empty($details)){
                $cart_details = null;
                for($i = 0; $i < count($details); $i++){
                        if($details[$i]->product_id == $request['product_id']){
                            $cart_details = $details[$i];
                        }
                }
                if(empty($cart_details)){
                    $cart_details = new CartDetail();
                    $cart_details->cart_id = $cart->id;
                }
                if (!empty($cart_details)){
                        $cart_details->product_id = $request['product_id'];
                        $cart_details->qty = $request['qty'];
                        $cart_details->price = $request['price'];
                        $cart_details->total = $request['qty'] * $request['price'];
                        $cart_details->total_text = number_format($request['qty'] * $request['price'],0,',','.') . "đ" ;
                        
                }
                
                if($request['qty'] == 0){
                    // dd($cart_details);
                    $cart_details->deleted_at = Carbon::now();
                    $cart_details->deleted_by = "remove product";
                }
                    $cart_details->save();
                    $details[0] = $cart_details;
                $info_payment = json_decode($cart->info_payment);
            // $info_payment[0]->total_price = $cart->total_price;
            // $info_payment[0]->total_price_text = number_format($cart->total_price,0,',','.') . " đ";
            // $info_payment[1]->total_price = $cart->total_pay;
            // $info_payment[1]->total_price_text = number_format($cart->total_pay,0,',','.') . " đ";
                $cart->info_payment = json_encode($info_payment);
            }
            $cart->total_price = $details->sum('total');
            
            $cart->total_pay = $details->sum('total');
            $cart->save();
            DB::commit();
            return $cart;
        }catch(\Exception $e) {
            DB::rollBack();
        //   dd($e);
            return ["data" => ["message" => $e]];
        }
    }
    public function updateCartInfo($req,$cart){
        try{
            DB::beginTransaction();
            // dd($req['address']);
            $cart->user_id = $req['user_id']??$cart->user_id;
            $cart->user_name = $req['username']??$cart->user_name;
            $cart->phone_number = $req['phone_number']??$cart->phone_number;
            $cart->address = $req['address']??$cart->address;
            $cart->payment = $req['payment']??$cart->payment;
            $cart->discount_code = $req['discount_code']??$cart->discount_code;
            $cart->discount_price = $req['discount_price']??$cart->discount_price;
            $cart->payment = $req['payment']??($cart->payment??"COD");
            $cart->note = $req['note']??$cart->note;
            $cart->promo_code = $req['promo_code']??$cart->promo_code;
            $cart->total_price = $req['total_price']??$cart->total_price;
            $cart->total_pay = $req['total_pay']??$cart->total_pay;
            $cart->updated_at = Carbon::now();
            $cart->updated_by = auth()->user()->id??"customer updated";

            if (!empty($cart->address)){
                $address_array = explode(",",$cart->address);
                
                $data_location = $this->getLocationForCart(trim($address_array[count($address_array) - 2]), trim($address_array[count($address_array) - 1]));
                if ($data_location){
                    $cart->lat = $data_location['data']['lat'];
                    $cart->lon = $data_location['data']['lon'];
                }
            }
            $cart->warehouse_id = $req['warehouse_id']??$cart->warehouse_id;

                $warehouses = json_decode($cart->warehouses);
                if (!empty($req['warehouse_id']) && !empty($warehouses)){
                    foreach($warehouses as $w){
                        if ($w->id == $cart->warehouse_id){
                            $cart->fee_ship = $w->price;
                            break;
                        }
                    }
                }
           

            $cart->save();

            DB::commit();
            return $cart;
        }catch(\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e;
        }      
    }
    public function getWarehouseNear($lat, $lon, $product_id){
        $nearestWarehouse = DB::select("
                SELECT w.id, w.code, w.name, wd.id, wd.product_id, wd.qty, w.lat, w.lon, 
                    (6371 * ACOS(
                        COS(RADIANS(?)) * COS(RADIANS(w.lat)) * 
                        COS(RADIANS(w.lon) - RADIANS(?)) + 
                        SIN(RADIANS(?)) * SIN(RADIANS(w.lat))
                    )) AS distance
                FROM warehouses w
                JOIN warehouse_details wd ON w.id = wd.warehouse_id
                WHERE wd.product_id = ?
                ORDER BY distance ASC
                LIMIT 1;
            ", [$lat, $lon, $lat, $product_id]);

            if (!empty($nearestWarehouse)) {
                // echo "Kho hàng gần nhất: " . $nearestWarehouse[0]->name;
                // echo " - Khoảng cách: " . round($nearestWarehouse[0]->distance, 2) . " km";
                // dd($nearestWarehouse);
                return ["data" => ["message" => "success","warehouse_code" => $nearestWarehouse[0]->code, "warehouse_name" => $nearestWarehouse[0]->name, "qty" => $nearestWarehouse[0]->qty, "far" => round($nearestWarehouse[0]->distance, 2), "unit" => "km"],"status" => 200];
            } else {
                return ["data" => ["message" => "not found"], "status" => 400];
            }
    }
    public function getWarehousesNear($lat, $lon, $product_ids){
        $product_id_lists = "";
        $total_qty = 10;
        foreach($product_ids as $i){
                $product_id_lists = $product_id_lists .  $i . ","; 
        }
        $product_id_lists = substr($product_id_lists, 0, -1);


        $nearestWarehouse = DB::select("
                 SELECT  w.id, w.code, w.name,  w.lat, w.lon,
                    ROUND(
						  (6371 * ACOS(
                        COS(RADIANS(?)) * COS(RADIANS(w.lat)) * 
                        COS(RADIANS(w.lon) - RADIANS(?)) + 
                        SIN(RADIANS(?)) * SIN(RADIANS(w.lat))
                    )), 2) AS distance
                    
                FROM warehouses w
                 WHERE    (SELECT id FROM warehouse_details wd WHERE wd.warehouse_id = w.id AND wd.product_id IN (?) AND wd.qty >= ?) > ?

                
            ", [$lat, $lon, $lat, $product_id_lists, $total_qty, count($product_ids)]);

            if (!empty($nearestWarehouse)) {
                // echo "Kho hàng gần nhất: " . $nearestWarehouse[0]->name;
                // echo " - Khoảng cách: " . round($nearestWarehouse[0]->distance, 2) . " km";
                // dd($nearestWarehouse);
                return ["data" => ["message" => "success","warehouses" => $nearestWarehouse,  "far" => round($nearestWarehouse[0]->distance, 2), "unit" => "km"],"status" => 200];
            } else {
                return ["data" => ["message" => "not found"], "status" => 400];
            }
    }
    public function getLocationForCart($district, $city)
    {
        //

        // $url = "https://nominatim.openstreetmap.org/search?city=" . urlencode($city);
        // //  . "&county=" . urlencode($district) . "&format=json&addressdetails=1";
        // if (!empty($req['district'])){
        //         $url .= "&county=" . urlencode($district) . "&county=" . urlencode($district);
        // }
        // $url .=  "&format=json&addressdetails=1";
        
        $response = Http::withHeaders([
            'User-Agent' => 'LaravelApp/1.0 (nthanhhuy82@gmail.com)' // Thay bằng email của bạn
        ])->get("https://nominatim.openstreetmap.org/search", [
            'city' => $city,
            'county' => $district,
            'format' => 'json',
            'addressdetails' => 1
        ]);
        $data = $response->json(); // Chuyển kết quả về JSON
        return ["data" => ["lat" => $data[0]['lat'], "lon" => $data[0]['lon']]];
    }

    public function priceForDistant($distant, $total_weight){
        $price = 16000;
            if ($total_weight > 3){
                $total_weight -= 3;

                $total_weight = round($total_weight / 3,0.9);
                for ($i = 1; $i <= $total_weight; $i ++){
                    $price += 2500;
                }
              
            }
            return $price;
    }
}