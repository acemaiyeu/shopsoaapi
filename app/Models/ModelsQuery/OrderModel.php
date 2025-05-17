<?php

namespace App\Models\ModelsQuery;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promotion;
use App\Models\Telegram;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderModel extends Model
{
    protected $promotion;

    public function __construct()
    {
        $this->promotion = new Promotion();
    }

    public function getAllOrders($request)
    {
        $query = Order::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        // $query->with('cart');
        $query->with('user');
        $query->with('details');
        $query->with('order_status');
        if (!empty($request['id'])) {
            $query->where('id', $request['id']);
        }
        if (!empty($request['code'])) {
            $query->where('code', $request['code']);
        }
        if (!empty($request['username'])) {
            $query->where('username', $request['username']);
        }
        if (!empty($request['start_time'])) {
            $query->where('created_at', '>=', $request['start_time']);
        }
        if (!empty($request['status'])) {
            $query->where('status', $request['status']);
        }
        if (!empty($request['end_time'])) {
            $query->where('created_at', '<=', $request['end_time']);
        }
        if (!empty($request['phone_number'])) {
            $query->where('phone_number', $request['phone_number']);
        }

        $query->orderBy('created_at', 'desc');

        $limit = $request['limit'] ?? 10;
        if ($limit == 1) {
            return $query->first();
        }
        if ($limit > 1) {
            return $query->paginate($limit);
        }
    }

    public function createOrder($req)
    {
        try {
            DB::beginTransaction();
            $cart = Cart::whereNull('deleted_at')->with('details')->where('id', $req['id'])->where('session', $req['session_id'])->first();
            if (empty($cart)) {
                return ['message' => 'Không tìm thấy giỏ hàng', 'status_code' => 400];
            }
            $order = new Order();
            $date = Carbon::now('Asia/Ho_Chi_Minh');
            $order->code = 'DH_' . $date->format('Y') . $date->format('m') . $date->format('d') . $date->format('H') . $date->format('i') . $date->format('s') . random_int(100, 999);
            $date = Carbon::now();
            // dd($date->format('H'),$date);

            $order->cart_id = $cart->id;
            $order->user_id = $cart->user_id ?? null;
            $order->fullname = $cart->fullname ?? $req['fullname'];
            $order->user_phone = $cart->phone_number ?? $req['user_phone'];
            $order->user_email = $cart->address ?? $req['user_email'];
            $order->discount_price = $cart->discount_price;

            $order->discount_code = $cart->discount_code;
            $order->total_price = $cart->total_price;

            $order->status = 1;

            $order->info_payment = $cart->info_payment;

            $order->save();
            foreach ($cart->details as $detail) {
                $detail_order = new OrderDetail();
                $detail_order->order_id = $order->id;
                $detail_order->theme_id = $detail->theme_id;
                $detail_order->quantity = $detail->quantity ?? 1;
                $detail_order->price = $detail->price;
                $detail_order->price_text = number_format($detail->price, 0, ',', '.') . ' đ';
                // $detail_order->discount_price = $detail->discount_price??0;
                // $detail_order->discount_price_text = number_format($detail->discount_price ?? 0,0,',','.') . " đ";
                // $detail_order->discount_name = $detail->discount_name??"";
                // $order->discount_price_text = number_format($detail->discount_price,0,',','.') . " đ";
                // $detail_order->discount_code = $detail->discount_code??"";
                $detail_order->total_price = $detail->total_price;
                $detail_order->total_price_text = number_format($detail->total_price, 0, ',', '.') . ' đ';
                $detail_order->save();
                $detail->deleted_at = $date;
                $detail->save();
            }

            $cart->deleted_at = $date;
            $cart->save();
            DB::commit();
            Telegram::sendMessage('Có đơn hàng mới ' . $order->code . ' vào lúc ' . Carbon::now('Asia/Ho_Chi_Minh')->format('H:i d/m/Y'));
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrder($req)
    {
        try {
            DB::beginTransaction();
            $order = Order::whereNull('deleted_at')->find($req['id']);
            if (empty($order)) {
                return response(['status' == 400, 'message' => 'Không tìm thấy đơn hàng'], 400);
            }
            $order->username = $req['username'] ?? $order->username;
            $order->phone_number = $req['phone_number'] ?? $order->phone_number;
            $order->address = $req['address'] ?? $order->address;
            $order->status = $req['status'] ?? $order->status;

            if ($order->status == 'APPROVED') {
                $order->status_text = 'Đã xử lý';
            }
            if ($order->status == 'SHIPPING') {
                $order->status_text = 'Đang giao hàng';
            }
            if ($order->status == 'SHIPPED') {
                $order->status_text = 'Đã giao hàng';
            }
            if ($order->status == 'COMPLETED') {
                $order->status_text = 'Đã hoàn thành';
            }
            if ($order->status == 'CANCEL') {
                $order->status_text = 'Đã hủy';
            }
            $order->save();
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
