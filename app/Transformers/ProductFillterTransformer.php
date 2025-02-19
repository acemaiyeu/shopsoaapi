<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProductFillter;
use Akaunting\Money\Money;

class ProductFillterTransformer extends TransformerAbstract
{

    public function transform(ProductFillter $product)
    {
        
        for ($i = 0; $i < $product->getFillters->count(); $i++){
            $product->getFillters[$i]->value = json_decode($product->getFillters[$i]->value);
        }

        return [
            'type' => $product->type,
            'fillters' => $product->getFillters,
            'created_at' => $product->created_at,
            'created_by' => $product->created_by
        ];
    }
}
