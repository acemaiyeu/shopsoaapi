<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProductFillter;
use Akaunting\Money\Money;
use Carbon\Carbon;


class FillterAdminTransformer extends TransformerAbstract
{

    public function transform(ProductFillter $product)
    {
        
        for ($i = 0; $i < $product->getFillters->count(); $i++){
            $product->getFillters[$i]->value = json_decode($product->getFillters[$i]->value);
        }

        return [
            'id'    => $product->id??null,
            'type' => $product->type,
            'details' => $product->getFillters,
            'created_at' => Carbon::parse($product->created_at)->format("d-m-Y"),
            // 'created_by' => $product->created_by,
            'createdBy'  => $product->createdBy->username??""
        ];
    }
}
