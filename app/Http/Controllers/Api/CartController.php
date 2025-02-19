<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\CartModel;
use App\Models\ModelsQuery\PromotionModel;
use App\Transformers\CartTransformer;
use App\Models\Product;
use App\Models\Cart;
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
                $cart = $this->promotionModel->getPromotionForCart($cart);
       }
       return fractal($cart, new CartTransformer())->respond();
    }

    public function addToCart(Request $req){
       
        $product = Product::find($req['product_id']);
        $message = "Không tìm thấy sản phẩm";
        $status = 401;
        $cart = null;
        
        if (!empty($product)){
            $message = "success";
            $status = 200;
            $cart = $this->cartModel->addToCart($req);
            // $cart = Cart::with('details')->find($cart->id);
        }
        return  fractal($cart, new CartTransformer())->respond(); 
    }   
    public function updateCartInfo(Request $req){
        $cart = $this->cartModel->getCart($req);
        $cart = $this->cartModel->updateCartInfo($req, $cart);
        return  fractal($cart, new CartTransformer())->respond(); 
    }
   
}
