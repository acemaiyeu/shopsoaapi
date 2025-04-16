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
use Illuminate\Support\Facades\Http;
use App\Models\Theme;

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
        // if (!empty($request['detailsSort'])){
        //     $query->with('detailsHaveProductSort');
        // }
           
            // $query->with('user');
            // dd($query->toSql());
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
                    $cart->method_payment = $request['method_payment']??"Zalopay";

                    if (!empty(auth()->user())){
                        $cart->user_id = auth()->user()->id;
                        $cart->fullname = auth()->user()->fullname??"";
                        $cart->user_phone = auth()->user()->phone??"";
                        $cart->user_email = auth()->user()->email??"";
                    }
                }
            }

            // $cart->user_id = !empty($cart->user_id)?$cart->user_id:(auth()->user->id??null);
            $cart->method_payment = $cart->method_payment??null;
            $cart->discount_code = $cart->discount_code??null;
            $cart->discount_price = $cart->discount_price??0;
            $cart->note = $cart->note??null;
            $cart->total_price = 0;
            if (!empty(auth()->user())){
                $cart->created_by = auth()->user()->id;
            }
            $cart->save();
            
            $details = CartDetail::whereNull('deleted_at')->where('cart_id', $cart->id)->get();
            $theme = Theme::whereNull('deleted_at')->where('id', $request['theme_id'])->first();
            if (empty($details)){
                $details = new CartDetail();
                $details->theme_id = $request['theme_id'];
                $details->quantity = $request['quantity'];
                $details->price = $theme->price ?? 100000000;
                $details->total_price = $details->quantity * $details->price;
                $details->save();
            } 
            if (!empty($details)){
                $cart_details = null;
                for($i = 0; $i < count($details); $i++){
                        if($details[$i]->theme_id == $request['theme_id']){
                            $cart_details = $details[$i];
                        }
                }
                if(empty($cart_details)){
                    $cart_details = new CartDetail();
                    $cart_details->cart_id = $cart->id;
                }
                if (!empty($cart_details)){
                        $cart_details->theme_id = $request['theme_id'];
                        $cart_details->quantity = $cart_details->quantity + $request['quantity'];
                        $cart_details->price = $theme->price ?? 100000000;
                        $cart_details->total_price = $cart_details->quantity * $cart_details->price;
                        $cart_details->total_text = number_format($cart_details->quantity * $cart_details->price,0,',','.') . "đ" ;
                        
                }
                
                if($request['quantity'] == 0){
                    // dd($cart_details);
                    $cart_details->deleted_at = Carbon::now();
                    $cart_details->deleted_by = 2;
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
            
            // $cart->total_price = $details->sum('total');
           
            $cart->save();
            DB::commit();
            return $cart;
        }catch(\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e;
        }
    }
    public function updateCartInfo($req,$cart){
        try{
            DB::beginTransaction();
            // dd($req['address']);
            if (!empty(auth()->user())){
                $cart->user_id = auth()->user()->id;
            }
            
           
            $cart->fullname = $req['fullname']??$cart->fullname;
            $cart->user_phone = $req['user_phone']??$cart->user_phone;
            $cart->user_address = $req['user_address']??$cart->user_address;
            $cart->user_email = $req['user_email']??$cart->user_email;
            $cart->method_payment = $req['method_payment']??($cart->payment??"Zalopay");
            $cart->note = $req['note']??$cart->note;
            $cart->updated_at = Carbon::now();
            $cart->updated_by = auth()->user()->id??null;
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
}