<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductFillter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductModel extends Model
{
    protected $model;
    public function __construct(Product $product) {
        $this->model = $product;
    }
    public function getAllProducts($request){
        $query =  Product::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['name'])){
            $query->where('name', 'like', '%' . $request['name'] . '%');
        }
        if(!empty($request['code'])){
            $query->orwhere('code', 'like', '%' . $request['code'] . '%');
        } 
        if (!empty($request['min_price'])){
                $query->where('price','>=', $request['min_price']);
        }
        if (!empty($request['max_price'])){
            $query->where('price','<=', $request['max_price']);
        }
        // dd($request['category_name']);
        if (!empty($request['category_name'])){
            $query->whereHas('category', function($query) use($request){
                    $query->where('name', 'like', "%" . $request['category_name'], "%");
            });
        }
        if (!empty($request['category_code'])){
                    $query->where('category_code',$request['category_code']);
        }
        if(!empty($request['brand'])){
            $cleanString = trim($request['brand'], "[]");
            // dd(explode(",", $cleanString));
            $query->whereIn('brand', explode(",", $cleanString));
        }   
        $query->whereHas('warehouse_details', function($query){
                $query->whereNull('deleted_at');
        });

        $limit = $request['limit'] ?? 10;
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
        
    }
    
    public function getAllProductsByAdmin($request){
        $query =  Product::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['name'])){
            $query->where('name', 'like', '%' . $request['name'] . '%');
        }
        if(!empty($request['code'])){
            $query->orwhere('code', 'like', '%' . $request['code'] . '%');
        } 
        if (!empty($request['min_price'])){
                $query->where('price','>=', $request['min_price']);
        }
        if (!empty($request['max_price'])){
            $query->where('price','<=', $request['max_price']);
        }
        if(!empty($request['brand'])){
            $cleanString = trim($request['brand'], "[]");
            // dd(explode(",", $cleanString));
            $query->whereIn('brand', explode(",", $cleanString));
        }   
        if(!empty($request['varian_product'])){
            $query->where('varian_product', $request['varian_product']);
        }
       

        $limit = $request['limit'] ?? 10;
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function getFillterForProductType($request){
        $query =  ProductFillter::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');

        $query->with('getFillters');
        $limit = $request['limit'] ?? 1;
        if (!empty($request['type'])){
            if (Str::contains(Str::lower($request['type']), 'điện thoại') || Str::contains(Str::lower($request['type']), 'smart phone')) {
                $query->where('type', 'like' , "%". 'điện thoại' ."%");
            }
            if (Str::contains(Str::lower($request['type']), 'tivi') || Str::contains(Str::lower($request['type']), 'tv')) {
                $query->where('type', 'like' , "%". 'tivi' ."%")->orwhere('type', 'like' , "%". 'tv' ."%");
            }
            if (Str::contains(Str::lower($request['type']), 'máy giặt') || Str::contains(Str::lower($request['type']), 'gia dụng')) {
                $query->where('type', 'like' , "%". 'máy giặt' ."%")->orwhere('type', 'like' , "%". 'gia dụng' ."%");
            }
            if (Str::contains(Str::lower($request['type']), 'tủ lạnh') || Str::contains(Str::lower($request['type']), 'gia dụng')) {
                $query->where('type', 'like' , "%". 'tủ lạnh' ."%")->orwhere('type', 'like' , "%". 'gia dụng' ."%");
            }
            if (Str::contains(Str::lower($request['type']), 'âm thanh') || Str::contains(Str::lower($request['type']), 'loa')) {
                $query->where('type', 'like' , "%". 'anh thanh' ."%")->orwhere('type', 'like' , "%". 'âm thanh' ."%")->orwhere('type', 'like' , "%". 'sound' ."%");
            }
            if (Str::contains(Str::lower($request['type']), 'default')) {
                $query->where('type', 'like' , "%". 'anh thanh' ."%")->orwhere('type', 'like' , "%". 'mặc định' ."%")->orwhere('type', 'like' , "%". 'chung' ."%")->orwhere('type', 'like' , "%". 'default' ."%");
            }   
            $query->orWhereHas('getFillters', function($query) use($request){
                $query->where('value', 'like', "%" . $request['type'] . "%");
            });
        }
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
        
    }
    public function createProduct($req){
        try {
            DB::beginTransaction();
            if (!empty($req['id'])){
                $product = Product::whereNull('deleted_at')->find($req['id']);
            }
            if (empty($req['id'])){
                $product = new Product();
            }
            $product->code = $req['code']??$product->code;
            $product->name = $req['name']??$product->name;
            $product->price = $req['price']??$product->price;
            $product->image_url = $req['image_url']?  str_replace("C:\\fakepath\\","",$req['image_url']):$product->image_url;
            $product->images = $req['images']?  json_encode($req['images']):$product->images;
            $product->brand = $req['brand']??$product->brand;
            $product->weight = $req['weight']??$product->weight;
            $product->weight_unit = $req['weight_unit']??$product->weight_unit;
            // $product->infomation_short = $req['infomation_short']??$product->infomation_short;
            $product->infomation_long = $req['infomation_long']??$product->infomation_long;
            $product->category_code = $req['category_code']??$product->category_code;
            $product->category_name = $req['category_name']??$product->category_name;
            $product->varians = !empty($req['varians'])?json_encode($req['varians']):null;
            $product->varian_product = $req['varian_product']??$product->varian_product;
            $product->save();
            
            DB::commit();
            return $product;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    }
}
