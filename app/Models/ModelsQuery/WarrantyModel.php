<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warranty;
use App\Models\Product;
use App\Models\WarehouseProductDetailStatus;
use App\Models\WarehouseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarrantyModel extends Model
{

    public function getWarrantyByCode($request){
        $query =  Warranty::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        // if (!empty($request['code'])){
        //     $query->where('code',$request['code']);
        // }
        // if (!empty($request['name'])){
        //     $query->where('name', 'like', "%" . $request['name'] . "%");
        // }
        // if (!empty($request['address'])){
        //     $query->where('address','like', "%" .  $request['address']. "%");
        // }

        // if (!empty($request['product_code'])){
        //     $query->whereHas('details', function ($query) use($request){
        //         $query->whereHas('product', function($query) use ($request){
        //             $query->where('code', $request['product_code']);
        //         });
        //     });
        // }
        // if (!empty($request['product_name'])){
        //     $query->whereHas('details', function ($query) use($request){
        //         $query->whereHas('product', function($query) use ($request){
        //             $query->where('name', 'like',"%" . $request['product_name'] . "%");
        //         });
        //     });
        // }

        // if (!empty($request['code'])){
        //     $query->where('code',$request['code']);
        // }

        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
   
}
