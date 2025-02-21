<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\WarehouseModel;
use App\Transformers\WarehouseTransformer;
use App\Transformers\WarehouseDetailTransformer;
use App\Transformers\WarehouseProductDetailTransformer;

use Illuminate\Support\Facades\Http;
class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $userModel;
    public function __construct(WarehouseModel $model) {
       $this->warehouseModel = $model;
    }
    public function getLocation(Request $req)
    {
        //
        $district = trim($req['district']);
        $city = trim($req['city']);
        // $url = "https://nominatim.openstreetmap.org/search?city=" . urlencode($city);
        // //  . "&county=" . urlencode($district) . "&format=json&addressdetails=1";
        // if (!empty($req['district'])){
        //         $url .= "&county=" . urlencode($district) . "&county=" . urlencode($district);
        // }
        // $url .=  "&format=json&addressdetails=1";
        
        $response = Http::withHeaders([
            'User-Agent' => 'LaravelApp/1.0 (nthanhhuy82@gmail.com)' // Thay bằng email của bạn
        ])->get("https://nominatim.openstreetmap.org/search", [
            'city' => $city,
            'county' => $district,
            'format' => 'json',
            'addressdetails' => 1
        ]);
        $data = $response->json(); // Chuyển kết quả về JSON
        return ["data" => ["lat" => $data[0]['lat'], "lon" => $data[0]['lon']]];
    }
    public function getAllWarehouse(Request $req){
           $warehouses =  $this->warehouseModel->getAllWarehouse($req);
           return fractal($warehouses, new WarehouseTransformer())->respond();
    }
    public function getWarehouseDetail(Request $req, $id){
        $req['warehouse_id'] = $id;
        $warehouse_detail =  $this->warehouseModel->getWarehouseDetail($req);
        return fractal($warehouse_detail, new WarehouseDetailTransformer())->respond();
    }
    public function getWarehouseProductDetail(Request $req, $id){
        $req['id'] = $id;
        $warehouse_product_details =  $this->warehouseModel->getWarehouseProductDetail($req);
        return fractal($warehouse_product_details, new WarehouseProductDetailTransformer())->respond();
                                                        
    }
    public function getWarehouseProductDetailByWarehouse(Request $req, $warehouse_id){
        $req['warehouse_id'] = $warehouse_id;
        $warehouse_product_details =  $this->warehouseModel->getWarehouseProductDetail($req);
        return fractal($warehouse_product_details, new WarehouseProductDetailTransformer())->respond();
                                                        
    }
    public function saveWarehouseProductDetail(Request $req){
        $this->warehouseModel->saveWarehouseProductDetail($req);
        return response(["data" => ["message" => "Thành công" . $req['product']]],200);
    }
    public function saveWarehouse(Request $req){
        $warehouse =  $this->warehouseModel->saveWarehouse($req);
        return fractal($warehouse, new WarehouseTransformer())->respond();
    }



}
