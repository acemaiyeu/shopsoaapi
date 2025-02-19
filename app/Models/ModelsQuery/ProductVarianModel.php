<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVarianModel extends Model
{

    public function getByProducts($request,$product_id){
        $query =  ProductVarian::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by')->where('product_id', $product_id);
    //     if($request['id'] <> ""){
    //         $query->where('id', $request['id']);
    //     }
    //     if($request['name'] <> ""){
    //         $query->where('name', 'like', '%' . $request['name'] . '%');
    //     }
    //     if($request['code'] <> ""){
    //         $query->orwhere('code', 'like', '%' . $request['code'] . '%');
    //     } 
    //     if (!empty($request['min_price'])){
    //             $query->where('price','>=', $request['min_price']);
    //     }
    //     if (!empty($request['max_price'])){
    //         $query->where('price','<=', $request['max_price']);
    // }
        // if($request['brand'] <> ""){
        //     $cleanString = trim($request['brand'], "[]");
        //     // dd(explode(",", $cleanString));
        //     $query->whereIn('brand', explode(",", $cleanString));
        // }   

        $query->with('product');
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function updateVarian($req){
            $varian = ProductVarian::whereNull('deleted_at')->find($req['id']);
            try{
                DB::beginTransaction();

                $varian->image_url = $req['image_url']??$varian->image_url;
                $varian->description = $req['description']??$varian->description;
                $varian->datas = $req['datas']?json_encode($req['datas']):$varian->image_url;
                $varian->price = $req['price']??$varian->price;
                $varian->save();
                
                DB::commit();
            }catch(Exception $e){
                    
                return response($e,400);
            }
            
    }
}
