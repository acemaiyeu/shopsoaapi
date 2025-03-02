<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Cart;
use Akaunting\Money\Money;

class CartTransformer extends TransformerAbstract
{
    
    public function transform(Cart $cart)
    {
        foreach($cart->details as $key => $detail){
            $product = $detail->product;
            $product->images = !empty($product->images)?json_decode($product->images,1):[];  
            $cart->details[$key]->product = $product;
        }
        return [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'username' => $cart->user_name,
            'phone_number' => $cart->phone_number,
            'payment' => $cart->payment,
            'discount_code' => $cart->discount_code,
            'discount_price' => $cart->discount_price,
            'session_id' => $cart->session??"",
            'payment' => $cart->payment,
            'note' => $cart->note,
            'details' => $cart->details,
            'detailsShort' => $cart->Short,
            'user' => $cart->user,
            'address' => $cart->address,
            'promo_code'  => $cart->promo_code,
            'total_price' => $cart->total_price,
            'info_payment' => json_decode($cart->info_payment),
            'gifts' => json_decode($cart->gifts),
            'warehouses' =>  json_decode($cart->warehouses), 
            // 'gifts' => [],
            'total_pay' => $cart->total_pay,
            'created_at'  => $cart->created_at,
        ];
    }
}
