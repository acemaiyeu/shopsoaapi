<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Discount;
use Carbon\Carbon;

class DiscountTransformer extends TransformerAbstract
{
    
    public function transform(Discount $discount)
    {
        return [
            'id' => $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'end_date' => Carbon::parse($discount->end_date)->format('d-m-Y H:i:s'),
            // 'conditions' => $discount->conditions,
            'created_at'  => $discount->created_at,
        ];
    }
}