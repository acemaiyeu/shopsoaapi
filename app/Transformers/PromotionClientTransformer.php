<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Promotion;
use Akaunting\Money\Money;
use App\Models\Product;

class PromotionClientTransformer extends TransformerAbstract
{
    protected $prodcts = [];
    public function __construct($products)
    {
        $this->products = $products;
    }

    public function transform(Promotion $promotion)
    {
            return [
                'id'            => $promotion->id,
                'code'          => $promotion->promotion_code,
                'name'          => $promotion->promotion_name,
                'start_time'    => $promotion->start_time,
                'end_time'      => $promotion->end_time,
                'conditions'    => $promotion->details,
                'status'        => $promotion->status,
                'conditions'    => json_decode($promotion->conditions),
                'gifts'         => json_decode($promotion->gifts),
                'condition_apply' => $promotion->condition_apply,
                'product_promotion' => $this->products??null,
                'img'               => $promotion->img??null,
                'created_at'    => $promotion->created_at,
            ];
        }
}
