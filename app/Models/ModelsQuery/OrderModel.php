<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\CartDetail;
use App\Models\ProductFillter;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ModelsQuery\PromoCodeModel;

class OrderModel extends Model
{
    protected $promotion;
    public function __construct(PromoCodeModel $promoCodeModel) {
        $this->promotion = new Promotion();
        $this->promoCodeModel = $promoCodeModel;
    }
    public function getAllOrders($request){
        $query =  Order::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        // $query->with('cart');
        $query->with('user');
        $query->with('details');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['code'])){
            $query->where('code', $request['code']);
        }
        if (!empty($request['username'])){
            $query->where('username', $request['username']);
        }
        if (!empty($request['user_id'])){
            $query->where('user_id', $request['user_id']);
        }
        if (!empty($request['start_time'])){
            $query->where('created_at', '>=', $request['start_time']);
        }
        if (!empty($request['status'])){
            $query->where('status', $request['status']);
        }
        if (!empty($request['end_time'])){
            $query->where('created_at', '<=', $request['end_time']);
        }
        if (!empty($request['phone_number'])){
            $query->where('phone_number', $request['phone_number']);
        }
        
        $query->orderBy("created_at",'desc');
        $limit = $request['limit'] ?? 10;
        if ($limit == 1){
            return $query->first();
        }
        if ($limit > 1){
            return $query->paginate($limit);    
        }
            
    }
    public function createOrder($req){
        try {
            DB::beginTransaction();
            $cart = Cart::whereNull('deleted_at')->with('details')->find($req['id']);
            if (empty($cart)){
                return ["message" => "Không tìm thấy giỏ hàng", "status_code" => 400];
            }
            $order = new Order();
                $date = Carbon::now('Asia/Ho_Chi_Minh');
                $order->code = "DH_" . $date->format('Y') . $date->format('m') . $date->format('d') . $date->format('H') . $date->format('i') . $date->format('s')  . random_int(100, 999);
                $date = Carbon::now();
                // dd($date->format('H'),$date);
                
                $order->cart_id = $cart->id;
                $order->user_id = $cart->user_id??null;
                $order->username = $cart->username??$req['username'];
                $order->phone_number = $cart->phone_number??$req['phone'];
                $order->address = $cart->address??$req['address'];
                $order->discount_price  = $cart->discount_price;
                
                $order->discount_code  = $cart->discount_code;
                $order->total_price = $cart->total_pay;
                
                $order->status = $req['status']??"PENDING";
                $order->status_text = $req['status_text']??"Chưa duyệt";
                $order->gifts = $cart->gifts??null;
                $order->info_payment = $cart->info_payment;
                
                $order->save();
                foreach($cart->details as $detail){
                    $detail_order = new OrderDetail();
                    $detail_order->order_id = $order->id;
                    $detail_order->product_id = $detail->product_id??null;
                    $detail_order->product_code = $detail->product->code;
                    $detail_order->product_name = $detail->product->code;
                    $detail_order->qty = $detail->qty??1;
                    $detail_order->price = $detail->price;
                    $detail_order->price_text = number_format($detail->price,0,',','.') . " đ";
                    $detail_order->discount_price = $detail->discount_price??0;
                    $detail_order->discount_price_text = number_format($detail->discount_price ?? 0,0,',','.') . " đ";
                    $detail_order->discount_name = $detail->discount_name??"";
                    // $order->discount_price_text = number_format($detail->discount_price,0,',','.') . " đ";
                    $detail_order->discount_code = $detail->discount_code??"";
                    $detail_order->total_price = $detail->total;
                    $detail_order->total_price_text = number_format($detail->total,0,',','.') . " đ";
                    $detail_order->save();
                    $detail->deleted_at = $date;
                    $detail->save();
                }
                
                $cart->deleted_at = $date;
                $cart->save();
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }     
    }
    public function updateOrder($req){
        try {
            DB::beginTransaction();
            $order = Order::whereNull('deleted_at')->find($req['id']);
        if (empty($order)){
            return response(["status" == 400, "message" => "Không tìm thấy đơn hàng"],400);
        }
            $order->username = $req['username']??$order->username;
            $order->phone_number = $req['phone_number']??$order->phone_number;
            $order->address = $req['address']??$order->address;
            $order->status = $req['status']??$order->status;

            if ($order->status == "APPROVED"){
                $order->status_text = "Đã xử lý";
            }
            if ($order->status == "SHIPPING"){
                $order->status_text = "Đang giao hàng";
            }
            if ($order->status == "SHIPPED"){
                $order->status_text = "Đã giao hàng";
            }
            if ($order->status == "COMPLETED"){
                $order->status_text = "Đã hoàn thành";
            }
            if ($order->status == "CANCEL"){
                $order->status_text = "Đã hủy";
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
