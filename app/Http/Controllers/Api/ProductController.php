<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use App\Transformers\ProductFillterTransformer;
use App\Transformers\ProductAdminTransformer;
use App\Models\ModelsQuery\ProductModel;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $product_model;
    public function __construct(ProductModel $product) {
        $this->product_model = $product;

    }
    public function index(Request $request)
    { 
        $products =  $this->product_model->getAllProducts($request);
        return fractal($products, new ProductTransformer())->respond();
    }
    
    public function indexByAdmin(Request $request)
    {   $request['limit'] = 1000;
        $products =  $this->product_model->getAllProductsByAdmin($request);
        return fractal($products, new ProductTransformer())->respond();
    }
    public function getFillterForProductType(Request $request){
        $products =  $this->product_model->getFillterForProductType($request);
        // $products->datas = json_decode($products->datas, true);
    //    return $products->getFillters();
        return fractal($products, new ProductFillterTransformer())->respond();
    }

    public function detail(Request $request, $id)
    {   $request['limit'] =1;
        $request['id'] = $id;
        $product = $this->product_model->getAllProducts($request);
        return fractal($product, new ProductTransformer())->respond();
    }   
    public function detailAdmin(Request $request, $id)
    {   $request['limit'] = 1;
        $request['id'] = $id;
        $product = $this->product_model->getAllProductsByAdmin($request);
        return fractal($product, new ProductAdminTransformer())->respond();
    }   
    public function create(Request $request)
    {
        $product = $this->product_model->createProduct($request);
        return fractal($product, new ProductTransformer())->respond();
    }   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::whereNull('deleted_at')->where('id',$id)->update(['deleted_at' => Carbon::now('Asia/Ho_Chi_Minh'), "deleted_by" => auth()->user()->id]);
        return response(["data" => ["message" => "Đã xóa thành công!"]],200);
    }
}
