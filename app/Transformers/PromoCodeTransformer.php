<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeTransformer extends TransformerAbstract
{
    
    public function transform(PromoCode $promo)
    {
        return [
            'id'                        => $promo->id,
            'code'                      => $promo->code,
            'name'                      => $promo->name,
            'total_discount'            => $promo->discounts,
            'total_discount_text'       => number_format($promo->discounts,0,',','.') . "đ",
            'type'                      => $promo->type,
            'start_date'                => Carbon::parse($promo->start_date)->format('d-m-Y H:i:s'),
            'end_date'                  => Carbon::parse($promo->end_date)->format('d-m-Y H:i:s'),
            'status'                    => $promo->status,
            'condition_info_apply'      => $promo->condition_info_apply,
            'discount'                  => json_decode($promo->discount),
            'condition_data'            => json_decode($promo->condition_data)??[],
            // 'discount_code' => $cart->discount_code,
            // 'discount_price' => $cart->discount_price,
            // 'session_id' => $cart->session??"",
            // 'note' => $cart->note,
            // 'details' => $cart->details,
            // 'address' => $cart->address,
            // 'promo_code'  => $cart->promo_code,
            // 'total_price' => $cart->total_price,
            // 'info_payment' => json_decode($cart->info_payment),
            // 'total_pay' => $cart->total_pay,
            'created_at'  => Carbon::parse($promo->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
