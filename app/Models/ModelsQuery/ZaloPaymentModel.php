<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Theme;
use Illuminate\Support\Facades\Http;
class ZaloPaymentModel extends Model
{
   
public function createZaloPayOrder(Request $request)
{
    $app_id = 'YOUR_APP_ID';
    $key1 = 'YOUR_KEY1';
    $endpoint = 'https://sb-openapi.zalopay.vn/v2/create'; // môi trường sandbox
    $amount = 100000; // số tiền đơn hàng (VND)
    $order_id = time() . ""; // mã đơn hàng riêng
    $embed_data = json_encode([]);
    $item = json_encode([]);

    $data = [
        "app_id" => $app_id,
        "app_trans_id" => date("ymd") . "_" . $order_id, // định dạng yêu cầu
        "app_user" => "user123",
        "app_time" => round(microtime(true) * 1000), // miliseconds
        "amount" => $amount,
        "item" => $item,
        "description" => "Thanh toán đơn hàng #" . $order_id,
        "embed_data" => $embed_data,
        "bank_code" => "zalopayapp" // có thể chọn bank khác
    ];

    // Tạo MAC bằng key1
    $data_string = $data["app_id"] . "|" . $data["app_trans_id"] . "|" . $data["app_user"] . "|" . $data["amount"] . "|" . $data["app_time"] . "|" . $data["embed_data"] . "|" . $data["item"];
    $mac = hash_hmac("sha256", $data_string, $key1);

    $data["mac"] = $mac;

    $response = Http::post($endpoint, $data);

    if ($response->successful()) {
        $result = $response->json();
        return redirect()->away($result['order_url']); // redirect user qua ZaloPay thanh toán
    } else {
        return response()->json(['error' => 'Gọi ZaloPay thất bại'], 500);
    }
}
public function handleZaloPayCallback(Request $request)
{
    $key2 = 'YOUR_KEY2';
    $data = $request->input('data');
    $reqMac = $request->input('mac');

    $calculatedMac = hash_hmac("sha256", $data, $key2);

    if ($reqMac !== $calculatedMac) {
        return response()->json(['return_code' => 1, 'return_message' => "Invalid MAC"]);
    }

    $order = json_decode($data, true);

    // Xử lý cập nhật trạng thái đơn hàng trong DB

    return response()->json(['return_code' => 1, 'return_message' => "success"]);
}

}