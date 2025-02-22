<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\CartModel;
use App\Models\ModelsQuery\PromotionModel;
use App\Transformers\CartTransformer;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Warehouse;
use App\Models\CartDetail;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    protected $cartModel;
    protected $promotionModel;
    public function __construct(CartModel $model, PromotionModel $promotionModelmodel) {
        $this->cartModel = $model;
        $this->promotionModel = $promotionModelmodel;
    }
    public function getCart(Request $req){
        $req['detailsSort'] = true;
       $cart =  $this->cartModel->getCart($req);
       if (!empty($cart->details)){
                if (empty($cart->user_id) && !empty(auth()->user()->id)){
                    $cart->user_id = auth()->user()->id;
                }
                if (empty($cart->user_name) && !empty(auth()->user()->name)){
                    $cart->user_name = auth()->user()->name;
                }
                if (empty($cart->phone_number) && !empty($cart->user->phone)){
                    $cart->phone_number = $cart->user->phone;
                }
                if (empty($cart->address) && !empty($cart->user->address)){
                    $cart->address = $cart->user->address;
                }
                
                $cart->save();
                if (!empty($cart->address)){
                    $array_address = explode(",",$cart->address);
                    // dd($array_address[count($array_address) - 2],$array_address,count($array_address) - 1);
                    $data = $this->cartModel->getLocationForCart($array_address[count($array_address) - 2],$array_address[count($array_address) -1]);
                    $cart->lat = $data['data']['lat'];
                    $cart->lon = $data['data']['lon'];
                }
                if (!empty($cart->details)){
                    $id_products =  $cart->details->pluck('product_id');
                    $warehouse = $this->cartModel->getWarehousesNear($cart->lat, $cart->lon, $id_products);
                    if (!empty($warehouse['data'])){
                            // dd($warehouse['data']['warehouses']);
                        // tinh tong so ky trong gio hang
                            $total_weight = 0;
                            foreach($cart->details as $detail){
                                if ($detail->product->weight_unit == "gram" || $detail->product->weight_unit == "g"){
                                    $total_weight = $total_weight  + ($detail->product->weight / 1000);
                                }
                                if ($detail->product->weight_unit == "kg"){
                                    $total_weight += $detail->product->weight;
                                }
                            }
                            if (!empty($warehouse['data']['warehouses'])){
                                foreach($warehouse['data']['warehouses'] as $key => $w){
                                    

                                    $w->price = $this->cartModel->priceForDistant($w->distance, $total_weight); 
                                    $w->price_text = number_format($w->price,0,',','.') . " đ";
                                    if (empty($cart->warehouse_id) && $key == 0){
                                        $cart->warehouse_id = $w->id;
                                        $cart->fee_ship = $w->price;
                                    }
                                }
                                $cart->warehouses = json_encode($warehouse['data']['warehouses']);
                            }
                            
                    }
                }
                $cart = $this->promotionModel->getPromotionForCart($cart);
       }
       return fractal($cart, new CartTransformer())->respond();
    }

    public function addToCart(Request $req){
       
        $product = Product::find($req['product_id']);
        $message = "Không tìm thấy sản phẩm";
        $status = 401;
        $cart = null;
            
            if (!(Warehouse::whereNull('deleted_at')->whereHas('details', function($query) use($req){
                $query->where('product_id', $req['product_id']);
            })->exists())){
                return response(["message" => "Các chi nhánh đã hết hàng. Mong quý khách thông cảm!"],400);
            }
        
        if (!empty($product)){
           
            $message = "success";
            $status = 200;
            $cart = $this->cartModel->addToCart($req);
            // $cart = Cart::with('details')->find($cart->id);
        }
        if(is_array($cart)){
            return response(["data" => ["message" => $cart['data']['message']]],400);
        }
        return  fractal($cart, new CartTransformer())->respond(); 
    }   
    public function updateCartInfo(Request $req){
        $cart = $this->cartModel->getCart($req);
        $cart = $this->cartModel->updateCartInfo($req, $cart);
        return response(["data" => ["messages" => "sessucess"]],200);
    }
   
}
