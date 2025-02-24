<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Warranty;
class OrderTransformer extends TransformerAbstract
{
    
    public function transform(Order $order)
    {
       $warranty =  Warranty::whereNull('deleted_at')->where('order_id',$order->id)->with('details')->first();
        return [
            'id'               => $order->id,
            'code'             => $order->code,
            'cart_id'          => $order->cart_id,
            'cart'             => $order->cart??null,
            'user_id'          => $order->user_id,
            'warehouse_id'     => $order->warehouse_id,
            'user'             => $order->user??null,
            'username'         => $order->username,
            'phone_number'     => $order->phone_number,
            'address'          => $order->address,
            'status'           => $order->status,
            'status_text'      => $order->status_text,
            'total_price'      => $order->total_price,
            'total_price_text' => number_format($order->total_price,0,',','.') . " đ",
            'details'          => $order->details,
            'info_payment'     => json_decode($order->info_payment),
            'gifts'            => json_decode($order->gifts),
            'warranty'         => $warranty,
            'created_at'       => Carbon::parse($order->created_at)->format('d-m-Y')
        ];
    }
}
