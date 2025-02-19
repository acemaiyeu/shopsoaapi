<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    //
    protected $product_model;
    public function __construct(Product $product) {
        $this->product_model = $product;
    }
    public function index(){
        $response = Http::get(env("URL_API") . "/v0/products");

        // Kiểm tra nếu API trả về thành công
        if ($response->successful()) {
            $data = $response->json();
            $fillter = $this->getFillterForProductType();
            return view("index", ['data' => $data, 'fillters' => $fillter]);
        }
        return response()->json(['message' => 'Lỗi khi gọi API products all'], 500);
    }
    public function getFillterForProductType(){
        $response = Http::get(env("URL_API") . "/v0/fillter/products?limit=1");

        // Kiểm tra nếu API trả về thành công
        if ($response->successful()) {
            $data = $response->json();
            return $data['data'];
        }
        return response()->json(['message' => 'Lỗi khi gọi API products all'], 500);
    }
}
