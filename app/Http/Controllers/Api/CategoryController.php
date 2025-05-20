<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryCreateValidator;
use App\Http\Requests\CategoryUpdateValidator;
use App\Models\ModelsQuery\CategoryModel;
use App\Models\ModelsQuery\ProductVarianModel;
use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model;

    public function __construct(CategoryModel $model)
    {
        $this->model = $model;
    }

    public function getCategory(Request $req)
    {
        $categories = $this->model->getCategory($req);
        return fractal($categories, new CategoryTransformer())->respond();
    }

    public function getDetail($id)
    {
        $req = ['id' => $id, 'limit' => 1];
        $categories = $this->model->getCategory($req);
        return fractal($categories, new CategoryTransformer())->respond();
    }

    public function getDetailByCode($code)
    {
        $req = ['code' => $code, 'limit' => 1];
        $categories = $this->model->getCategory($req);
        return fractal($categories, new CategoryTransformer())->respond();
    }

    public function create(CategoryCreateValidator $req)
    {
        $data = $req->validated();
        $category = $this->model->createOrUpdate($data);

        if (is_array($category)) {
            return response()->json($category, $category['status']);
        }
        return fractal($category, new CategoryTransformer())->respond();
    }

    public function update(CategoryUpdateValidator $req)
    {
        $data = $req->validated();
        $category = $this->model->createOrUpdate($data);

        if (is_array($category)) {
            return response()->json($category, $category['status']);
        }
        return fractal($category, new CategoryTransformer())->respond();
    }

    public function deleteCategory($id)
    {
        $category = Category::whereNull('deleted_at')->find($id);
        if (!$category) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy dữ liệu'], 404);
        }
        $category->update(['deleted_at' => now(), 'deleted_by' => auth()->user()->id]);
        return response()->json(['status' => 200, 'message' => 'Xóa danh sách thành công'], 200);
    }
}
