<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\PromoCodeTransformer;
use App\Models\PromoCode;
use App\Models\ModelsQuery\CartModel;
use App\Models\ModelsQuery\PromoCodeModel;
use Carbon\Carbon;

class PromoCodeController extends Controller
{
    protected $promoCodeModel;
    protected $cartModel;
    public function __construct(PromoCodeModel $model, CartModel $cartMdel) {
        $this->promoCodeModel = $model;
        $this->cartModel = $cartMdel;
    }
    public function getPromoCode(Request $req){
       $promo_codes =  $this->promoCodeModel->getPromoCode($req);
       return  fractal($promo_codes, new PromoCodeTransformer())->respond(); 
    }
    public function getPromoCodeClient(Request $req){
        $req['start_date'] = Carbon::now();
        $req['end_date'] = Carbon::now();
        $req['status'] = 1;
        $promo_codes =  $this->promoCodeModel->getPromoCode($req);
        return  fractal($promo_codes, new PromoCodeTransformer())->respond(); 
     }
    public function addPromoCode(Request $req){
        $cart =  $this->cartModel->getCart($req);
        $this->promoCodeModel->addPromoCode($req, $cart);
        return  ['data' => ["message" => "success", "status" => 200]];
    }
    public function detail($id){
          $promo = PromoCode::whereNull('deleted_at')->find($id);
        if (empty($promo)){
            return response(["message" => "Không tìm thấy mã giảm giá"], 400);
        }
          return  fractal($promo, new PromoCodeTransformer())->respond(); 
    }
    public function update(Request $req){

        $promo = PromoCode::whereNull('deleted_at')->find($req['id']);
        $promo->discount = json_encode($req['discount']);
        $promo->save();
    }
}
