<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\PromotionTransformer;
use App\Transformers\PromotionClientTransformer;
use App\Transformers\ProductTransformer;
use App\Models\ModelsQuery\PromotionModel;
use App\Models\ModelsQuery\ProductModel;
use Carbon\Carbon;
use App\Models\Promotion;
use App\Models\Product;
class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $promotion_model;
    protected $product_model;
    protected $products  = [];
    public function __construct(PromotionModel $model, ProductModel $product) {
        $this->promotion_model = $model;
        $this->product_model = $product;
    }
    public function getPromotions(Request $req){
           $promotions = $this->promotion_model->getPromotions($req);
           return fractal($promotions, new PromotionTransformer())->respond();
    }
    public function getProductPromotions(Request $req){
        $products =  $this->product_model->getAllProducts($req);
        return fractal($products, new ProductTransformer())->respond();
    }
    public function getDetail(Request $req, $id){
        $req['id'] = $id;
        $req['limit'] = 1;
        $promotion =  $this->promotion_model->getPromotions($req);
        return fractal($promotion, new PromotionTransformer())->respond();
    }
    public function create(Request $req){
        $promotion =  $this->promotion_model->createPromotions($req);
        return fractal($promotion, new PromotionTransformer())->respond();
        // return $promotion;
    }
    public function deleteById(Request $req, $id){
        $promotion =  Promotion::whereNull('deleted_at')->find($id);
        if (!empty($promotion)){
            $promotion->deleted_at = Carbon::now();
            $promotion->save();
        }
        return response(["data" => ["message"]],200);
        // return $promotion;
    }
    public function getPromotionsForWeb(Request $req){
        $req['show_web'] = 1;
        $req['start_time'] = Carbon::now();
        $req['end_time'] = Carbon::now();
        $req['status'] = 1;
        $promotions = $this->promotion_model->getPromotions($req);
        foreach($promotions as $key => $promotion){
                if ($this->checkPromotionAccept(json_decode($promotion->gifts)) == false){
                    unset($promotions[$key]);
                }
        }
        return fractal($promotions, new PromotionClientTransformer($this->products))->respond();
    }

    public function checkPromotionAccept($gifts_promotion){
        $accept = false;
            foreach($gifts_promotion as $gift){
                if ($gift->type == "DISCOUNT_PRICE"){
                    foreach($gift->gifts as $item){
                            $product = Product::whereNull('deleted_at')->where('code', $item->product_code)->select('id','price','name','images','rates')->first();
                            if (!empty($product)){
                                $product->images = json_decode($product->images);
                                // $product[] = $product;
                                $discount = 0;
                                if ($item->type_discount == "price"){
                                    $product->discount_price_text = number_format($product->price - $item->value,0,',','.') . " đ";
                                    if ($product->price - $item->value < 0){
                                        $product->discount_price_text = number_format(0,0,',','.') . " đ";
                                    }
                                    $product->discount_percent_text = (($product->price - $item->value) / $product->price) / 100 . "%";
                                    $product->discount_old_price_text = number_format($product->price,0,',','.') . " đ";
                                    
                                    $this->products[] = $product;
                                }
                                if ($item->type_discount == "percent"){
                                    
                                    $discount = ($product->price * $item->value) / 100;
                                    $product->discount_price_text = number_format($product->price - $discount,0,',','.') . " đ";
                                    if ($product->price - $discount < 0){
                                        $product->discount_price_text = number_format(0,0,',','.') . " đ";
                                        
                                    }
                                    $product->discount_percent_text = $item->value . "%";
                                    $product->discount_old_price_text = number_format($product->price,0,',','.') . " đ";
                                    // dd($product->discount_percent_text);
                                    $this->products[] = $product;
                                }
                                $accept = true;
                            }
                    }
                }             
                
            }

        return $accept;
    }
}