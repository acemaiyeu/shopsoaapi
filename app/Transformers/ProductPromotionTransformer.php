<?php

namespace App\Transformers;

use Akaunting\Money\Money;
use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductPromotionTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
        ];
    }
}
