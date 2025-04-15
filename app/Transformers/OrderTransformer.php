<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Order;
use Akaunting\Money\Money;
use Carbon\Carbon;
class OrderTransformer extends TransformerAbstract
{
    
    public function transform(Order $order)
    {
        return [
            'id'               => $order->id,
            'code'             => $order->code,
            'cart_id'          => $order->cart_id,
            'cart'             => $order->cart??null,
            'user_id'          => $order->user_id,
            'user'             => $order->user??null,
            'username'         => $order->username,
            'user_phone'     => $order->user_phone,
            'address'          => $order->address,
            'status'           => $order->order_status,
            'fullname'         => $order->fullname,
            'status_text'      => $order->status_text,
            'total_price'      => $order->total_price,
            'total_price_text' => number_format($order->total_price,0,',','.') . " đ",
            'details'          => $order->details,
            'info_payment'     => json_decode($order->info_payment),
            'gifts'            => json_decode($order->gifts),
            'created_at'       => Carbon::parse($order->created_at)->format('d-m-Y')
        ];
    }
}