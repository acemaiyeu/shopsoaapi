<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\ProductFillterModel;
use App\Transformers\FillterAdminTransformer;
use App\Models\ProductFillter;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
class ProductFillterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $fillterModel;
    public function __construct(ProductFillterModel $model) {
       $this->fillterModel = $model;
    }
    
    public function getALLFillter(Request $req){
           $fillters =  $this->fillterModel->getALLFillter($req);
           return fractal($fillters, new FillterAdminTransformer())->respond();
    }
    public function getDetailFillter(Request $req){
        $req['limit'] = 1;
       $fillter =  $this->fillterModel->getALLFillter($req);
       return fractal($fillter, new FillterAdminTransformer())->respond();
}

    public function saveFillter(Request $req){
        if (!is_array(['details'])){
            return response(["data" => ['message' => "Dữ liệu phải là array"]], 400);
        }
        $fillter =  $this->fillterModel->saveFillter($req);
        if (is_array($fillter)){
            return response(["data" => ['message' => $fillter['message']]], 400);
        }
            return response(["data" => ['message' => "Đã lưu thành công!"]]);
    }
    public function deleteFillter(Request $req, $id){
        ProductFillter::whereNull('deleted_at')->where('id', $id)->update(['deleted_at' => Carbon::now('Asia/Ho_Chi_Minh'), "deleted_by" => auth()->user()->id]);
        return response(["data" => ['message' => "Cập nhật thành công!"]]);
    }
}
