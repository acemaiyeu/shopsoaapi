<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Product;
use Akaunting\Money\Money;

class ProductTransformer extends TransformerAbstract
{
    
    public function getVarianTogether($varian_product){
        $products =  Product::whereNull('deleted_at')->where('varian_product',$varian_product)->get()->select('id','varians','image_url','varian_product', );
        $products_2 = [];
        foreach($products as $product){
            if (!empty($product['varians'])){
                $product['varians'] = json_decode($product['varians']);
            }
            
            $products_2[] = $product;
        }
        return $products_2;
    }
    public function totalRating($ratings){
        $total_rate = 0;
        for($i = 0; $i < 10000; $i++){
            if(empty($ratings[$i])){
                break;
            }
            $total_rate += (int) $ratings[$i]->rating;
        }
        return $total_rate;
    }
    public function transform(Product $product)
    {
      
        
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->price,
            'image_url' => $product->image_url,
            'images' => json_decode($product->images)??[],
            'price_show' => number_format($product->price,0,',','.') . " đ",
            'brand' => $product->brand,
            'discount' => $product->discount,
            'infomation_short' => json_decode($product->infomation_short, true),
            'infomation_long' => $product->infomation_long ??null,
            'varians'           => json_decode($product->varians),
            'weight'            => $product->weight,
            'weight_unit'       => $product->weight_unit,
            "ratings"           =>json_decode($product->rates),
            'total_rating'      => $this->totalRating(json_decode($product->rates), true),
            'varians'           => $this->getVarianTogether($product->varian_product),
            'category'          => $product->category
        ];
    }

}
