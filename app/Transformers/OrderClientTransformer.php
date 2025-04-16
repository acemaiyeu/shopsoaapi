<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Order;
use Akaunting\Money\Money;
use Carbon\Carbon;
class OrderClientTransformer extends TransformerAbstract
{
    
    public function transform(Order $order)
    {
        $change_email = substr($order->user_email, 0, 4) . '***' . substr($order->user_email, -4);
        $change_phone = substr($order->user_phone, 0,3) . '*****' . substr($order->user_phone, -2);
        return [
            'id'               => $order->id,
            'code'             => $order->code,
        
            'user_id'          => $order->user_id,
            'user'             => $order->user??null,
            'fullname'         => $order->fullname,
            'user_phone'     => $change_phone,
            'user_email'     => $change_email,
          
            'status'           => $order->order_status,
          
            'total_price'      => $order->total_price,
            'total_price_text' => number_format($order->total_price,0,',','.') . " đ",
            'details'          => $order->details,
            'info_payment'     => json_decode($order->info_payment),
            'gifts'            => json_decode($order->gifts),
            'created_at'       => Carbon::parse($order->created_at)->format('d-m-Y')
        ];
    }
}