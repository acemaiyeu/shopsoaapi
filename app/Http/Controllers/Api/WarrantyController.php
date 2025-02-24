<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\WarrantyModel;
use App\Transformers\WarrantyTransformer;

use Illuminate\Support\Facades\Http;
class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $warrantyModel;
    public function __construct(WarrantyModel $model) {
       $this->warrantyModel = $model;
    }
    
    public function getWarrantyByCode(Request $req){
            $req['limit'] = 1;
           $warranty =  $this->warrantyModel->getWarrantyByCode($req);
           return fractal($warranty, new WarrantyTransformer())->respond();
    }
    public function getWarrantyByWarhouseAndOrder(Request $req, $warehouse_id, $order_id){
        $req['warehouse_id'] = $warehouse_id;
        $req['order_id'] = $order_id;
        $req['limit']  = 1;
        $warranty =  $this->warrantyModel->getWarrantyByCode($req);
        return fractal($warranty, new WarrantyTransformer())->respond();
    }
    public function updateOrderWarranty(Request $req){
        $order = $req['order'];
        if (!is_array($order['details'])){
            return response(["data" => ['message' => "Dữ liệu phải là array"]], 400);
        }
        $warranty =  $this->warrantyModel->updateOrderWarranty($order);
        if (is_array($warranty)){
            return response(["data" => ['message' => $warranty['message']]], 400);
        }
            return response(["data" => ['message' => "Cập nhật thành công!"]]);
    }
    public function updateWarrantyDetail(Request $req){
        if (!is_array($req['details'])){
            return response(["data" => ['message' => "Dữ liệu phải là array"]], 400);
        }
        $warranty =  $this->warrantyModel->updateWarrantyDetail($req['details']);
        if (is_array($warranty)){
            return response(["data" => ['message' => $warranty['message']]], 400);
        }
            return response(["data" => ['message' => "Cập nhật thành công!"]]);
    }
}
