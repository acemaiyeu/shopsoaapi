<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ModelsQuery\OrderModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Transformers\OrderTransformer;
use App\Http\Requests\confirmOrderValidate;


class OrderController extends Controller
{
    protected $orderModel;
    public function __construct(OrderModel $orderM) {
        $this->orderModel = $orderM;
    }

    public function statisticsOrders(){
        $date = Carbon::now();
        $day7 = $date->format('Y-m-d');
        $total_price_7 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day7 . "%")->sum('total_price');
        $date = $date->subDay();
        $day6 = $date->format('Y-m-d');
        $total_price_6 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day6 . "%")->sum('total_price');
        $date = $date->subDay();
        $day5 = $date->format('Y-m-d');
        $total_price_5 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day5 . "%")->sum('total_price');
        $date = $date->subDay();
        $day4 = $date->format('Y-m-d');
        $total_price_4 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day4 . "%")->sum('total_price');
        $date = $date->subDay();
        $day3 = $date->format('Y-m-d');
        $total_price_3 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day3 . "%")->sum('total_price');
        $date = $date->subDay();
        $day2 = $date->format('Y-m-d');
        $total_price_2 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day2 . "%")->sum('total_price');
        $date = $date->subDay();
        $day1 = $date->format('Y-m-d');
        $total_price_1 = Order::whereNull('deleted_at')->where('created_at','like', "%" . $day1 . "%")->sum('total_price');

        $data = [["date" => $day7, "amount" => $total_price_7],
        ["date" => $day6,"amount" => $total_price_6],
        ["date" => $day5,"amount" => $total_price_5],
        ["date" => $day4,"amount" => $total_price_4],
        ["date" => $day3,"amount" => $total_price_3],
        ["date" => $day2,"amount" => $total_price_2],
        ["date" => $day1,"amount" => $total_price_1]];
        return $data;
    }
    public function getAllOrders(Request $request){
            $orders = $this->orderModel->getAllOrders($request);
            return fractal($orders, new OrderTransformer())->respond();
    }
    public function confirmOrder(confirmOrderValidate $req){
       
       $order =  $this->orderModel->createOrder($req);
       if ($order['status_code'] == 400){
        return response(["data" => ["message" => $order['message']]],400);
       }
        return fractal($order, new OrderTransformer())->respond();
    }
    public function detail(Request $req, $phone, $code){
        $req['limit'] = 1;
        $req['code'] = $code;
        $req['phone_number'] = $phone;
        $order =  $this->orderModel->getAllOrders($req);
        if (!empty($order['status_code'])){
            return response($order['message'],400);
           }
        //    return $order;
        return $order;
            return fractal($order, new OrderTransformer())->respond();
    }
    public function detailAdmin(Request $req, $id){
        $req['limit'] = 1;
        $req['id'] = $id;
        $order =  $this->orderModel->getAllOrders($req);
        if (!empty($order['status_code'])){
            return response($order['message'],400);
           }
        //    return $order;
            return fractal($order, new OrderTransformer())->respond();
    }
    public function updateOrder(Request $req){
        $order =  $this->orderModel->updateOrder($req);
        if (!empty($order['status_code'])){
            return response(['message' => $order['message']],400);
           }
        return response(['message' => "Cập nhật trạng thái thành công"],200);
    }
    public function getStatus($id){
        $order = Order::whereNull('deleted_at')->find($id);
        // ->select('status','status_text');
        if (empty($status)){
            response(["message" => "Không tìm thấy đơn hàng",400]);
        }
        return response(["message" => ["status" => $order->status, "status_text" => $order->status_text], 200]);
    }
    public function myOrder(Request $req){
        
        if (!empty(auth()->user())){
            $req['user_id'] = auth()->user()->id; 
        }
        if(empty(auth()->user()) && empty($req['code'])){
            return response(["data" => ["data" => []]],200);
        }

        $orders = $this->orderModel->getAllOrders($req);
        return fractal($orders, new OrderTransformer())->respond();
    }   
    public function ratingOrder(Request $req){
        if (!empty(auth()->user())){
            $order = $this->orderModel->ratingOrder($req);
            if (is_array($order)){
                return response(["data" => ["message" => $order['message']]],400);
            }
            return fractal($order, new OrderTransformer())->respond();
        }
        return response(["data" => ["message" => "Không tìm thấy đơn hàng của bạn. Chắc chắn bạn đã đăng nhập?"]],400)  ;
    }   
    
}
