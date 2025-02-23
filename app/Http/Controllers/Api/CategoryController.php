<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\CategoryModel;
use App\Transformers\CategoryTransformer;

use Illuminate\Support\Facades\Http;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $categoryModel;
    public function __construct(CategoryModel $model) {
       $this->categoryModel = $model;
    }
    

    public function getAllCategoryForAdmin(Request $req){
        $req['limit'] = 1;
       $warranty =  $this->categoryModel->getWarrantyByCode($req);
       return fractal($warranty, new CategoryTransformer())->respond();
    }
    public function getDetailCategoryForAdmin(Request $req, $warehouse_id, $order_id){
        $req['warehouse_id'] = $warehouse_id;
        $req['order_id'] = $order_id;
        $req['limit']  = 1;
        $warranty =  $this->categoryModel->getWarrantyByCode($req);
        return fractal($warranty, new CategoryTransformer())->respond();
    }
    public function deleteById(Request $req){
        $req['limit'] = 1;
    $warranty =  $this->categoryModel->getWarrantyByCode($req);
        return fractal($warranty, new CategoryTransformer())->respond();
    }
}
