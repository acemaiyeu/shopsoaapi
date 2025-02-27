<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\ProductVarianModel;
use App\Transformers\ProductVarianTransformer;
use App\Models\ModelsQuery\ProductModel;
use App\Models\Product;
use App\Transformers\ProductAdminTransformer;

class ProductVarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $productVarianModel;
    protected $product_model;

    public function __construct(ProductVarianModel $product, ProductModel $model) {
        $this->productVarianModel = $product;
        $this->product_model = $model;
    }
    public function getAllByProduct(Request $req, $product_id){
        $productVarians = $this->productVarianModel->getByProducts($req, $product_id);
        return  fractal($productVarians, new ProductVarianTransformer())->respond(); 
    } 
    public function update(Request $req){
        $productVarian = $this->productVarianModel->updateVarian($req);
        return  ["status" => 200];
    }  
    public function getAllByProductVarians(Request $req, $id){
        $product = Product::whereNull('deleted_at')->select('id','varian_product')->find($id);
        if (empty($product)){
            return response(["data" => ["message" => "Không tìm thấy sản phẩm!"]],400);
        }
        $req['varian_product'] = $product->varian_product;
        $products =  $this->product_model->getAllProductsByAdmin($req);
        return fractal($products, new ProductAdminTransformer())->respond();
    }
}
