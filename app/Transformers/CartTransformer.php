<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Cart;
use Akaunting\Money\Money;

class CartTransformer extends TransformerAbstract
{
    
    public function transform(Cart $cart)
    {
        return [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'fullname' => $cart->fullname,
            'user_phone' => $cart->user_phone,
            'user_email' => $cart->user_email,
            'payment' => $cart->payment,
            'discount_code' => $cart->discount_code,
            'discount_price' => $cart->discount_price,
            'session_id' => $cart->session??"",
            'method_payment' => $cart->method_payment,
            'note' => $cart->note,
            'details' => $cart->details,
            'detailsShort' => $cart->Short,
            'user' => $cart->user,
            'address' => $cart->address,
            'promo_code'  => $cart->promo_code,
            'total_price' => $cart->total_price,
            'info_payment' => json_decode($cart->info_payment),
            'gifts' => json_decode($cart->gifts),
            // 'gifts' => [],
            'total_pay' => $cart->total_pay,
            'created_at'  => $cart->created_at,
        ];
    }
}