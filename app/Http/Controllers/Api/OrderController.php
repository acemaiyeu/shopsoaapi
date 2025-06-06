<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\confirmOrderValidate;
use App\Models\ModelsQuery\OrderModel;
use App\Models\Order;
use App\Models\User;
use App\Transformers\OrderClientTransformer;
use App\Transformers\OrderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderModel;

    public function __construct(OrderModel $orderM)
    {
        $this->orderModel = $orderM;
    }

    public function statisticsOrders()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('m');

            $data[] = [
                'month' => $month,
                'orders' => Order::whereNull('deleted_at')->whereMonth('created_at', $month)->where('status', '<', 3)->count(),
                'customers' => User::whereNull('deleted_at')->whereMonth('created_at', $month)->count()
            ];
        }
        return $data;
    }

    public function statisticsOrdersRevenue()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('m');
            $months[] = Number_format(Order::whereNull('deleted_at')->whereMonth('created_at', $month)->where('status', '<', 3)->sum('total_price'));
        }
        return $months;
    }

    public function getAllOrders(Request $request)
    {
        $orders = $this->orderModel->getAllOrders($request);
        return fractal($orders, new OrderTransformer())->respond();
    }

    public function getAllOrdersByClient(Request $request)
    {
        if (empty(auth()->user())) {
            return response(['message' => 'Vui lòng đăng nhập'], 400);
        }
        $request['user_id'] = auth()->user()->id;
        $orders = $this->orderModel->getAllOrders($request);
        return fractal($orders, new OrderClientTransformer())->respond();
    }

    public function confirmOrder(confirmOrderValidate $req)
    {
        // $data = $request->validated();

        $order = $this->orderModel->createOrder($req);
        if ($order['status_code'] == 400) {
            return response($order['message'], 400);
        }
        return fractal($order, new OrderTransformer())->respond();
    }

    public function detail(Request $req, $phone, $code)
    {
        $req['limit'] = 1;
        $req['code'] = $code;
        $req['phone_number'] = $phone;
        $order = $this->orderModel->getAllOrders($req);
        if (!empty($order['status_code'])) {
            return response($order['message'], 400);
        }
        //    return $order;
        return fractal($order, new OrderTransformer())->respond();
    }

    public function detailAdmin(Request $req, $id)
    {
        $req['limit'] = 1;
        $req['id'] = $id;
        $order = $this->orderModel->getAllOrders($req);
        if (!empty($order['status_code'])) {
            return response($order['message'], 400);
        }
        //    return $order;
        return fractal($order, new OrderTransformer())->respond();
    }

    public function updateOrder(Request $req)
    {
        $order = $this->orderModel->updateOrder($req);
        if (!empty($order['status_code'])) {
            return response(['message' => $order['message']], 400);
        }
        return response(['message' => 'Cập nhật trạng thái thành công'], 200);
    }

    public function getStatus($id)
    {
        $order = Order::whereNull('deleted_at')->find($id);
        // ->select('status','status_text');
        if (empty($status)) {
            response(['message' => 'Không tìm thấy đơn hàng', 400]);
        }
        return response(['message' => ['status' => $order->status, 'status_text' => $order->status_text], 200]);
    }

    public function detailByCode(Request $req, $code)
    {
        $req['limit'] = 1;
        $req['code'] = $code;
        $order = $this->orderModel->getAllOrders($req);
        if (!empty($order['status_code'])) {
            return response($order['message'], 400);
        }
        //    return $order;
        return fractal($order, new OrderClientTransformer())->respond();
    }
}
