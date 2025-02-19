<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProductVarian;
use Akaunting\Money\Money;

class ProductVarianTransformer extends TransformerAbstract
{
    
    public function transform(ProductVarian $varian)
    {
        return [
            'id' => $varian->id,
            'product_id'  => $varian->product_id,
            'image_url'  => $varian->image_url,
            'description' => $varian->description,
            'price'       => $varian->price,
            'price_text'       => number_format($varian->price,0,',','.') . " đ",
            'datas'       => json_decode($varian->datas),
            'product'     => $varian->product,
            'created_at'  => $varian->created_at,
        ];
    }
}
