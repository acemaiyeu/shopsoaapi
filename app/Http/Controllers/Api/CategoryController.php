<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\CategoryModel;
use App\Transformers\CategoryTransformer;
use Carbon\Carbon;

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
       $warranty =  $this->categoryModel->getAllCategory($req);
       return fractal($warranty, new CategoryTransformer())->respond();
    }
    public function getDetailCategoryForAdmin(Request $req, $code){
        $req['limit']  = 1;
        $req['code'] = $code;
        $category =  $this->categoryModel->getAllCategory($req);
        return fractal($category, new CategoryTransformer())->respond();
    }
    public function saveCategory(Request $req){
        $category =  $this->categoryModel->saveCategory($req);
        return $category;
        return fractal($category, new CategoryTransformer())->respond();
    }

    
    public function deleteByCode(Request $req){
        Category::whereNull('deleted_at')->where('code', $req['code'])->update(['deleted_at' => Carbon::now('Asia/Ho_Chi_Minh'), "deleted_by" => auth()->user()->id]);
        return response(["data" => ["message" => "Đã xóa thành công!"]],200);
    }
}
