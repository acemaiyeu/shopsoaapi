<?php

namespace App\Transformers;

use App\Models\Coupon;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    public function transform(Coupon $coupon)
    {
        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'active' => $coupon->active,
            'conditions' => $coupon->conditions ?? [],
            'condition_apply' => $coupon->condition_apply,
            'data' => $coupon->data,
            'start_date' => Carbon::parse($coupon->created_at)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::parse($coupon->created_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($coupon->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
