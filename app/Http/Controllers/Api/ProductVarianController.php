<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\ProductVarianModel;
use App\Transformers\ProductVarianTransformer;
use App\Models\ModelsQuery\ProductModel;

class ProductVarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $productVarianModel;
    public function __construct(ProductVarianModel $product) {
        $this->productVarianModel = $product;
    }
    public function getAllByProduct(Request $req, $product_id){
        $productVarians = $this->productVarianModel->getByProducts($req, $product_id);
        return  fractal($productVarians, new ProductVarianTransformer())->respond(); 
    } 
    public function update(Request $req){
        $productVarian = $this->productVarianModel->updateVarian($req);
        return  ["status" => 200];
    }  
}
