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
}
