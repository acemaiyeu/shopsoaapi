<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warranty;
use App\Models\Product;
use App\Models\WarrantyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarrantyModel extends Model
{

    public function getWarrantyByCode($request){
        $query =  Warranty::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['warehouse_id'])){
            $query->where('warehouse_id',$request['warehouse_id']);
        }
        if (!empty($request['order_id'])){
            $query->where('order_id',$request['order_id']);
        }
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
        $query->with('details');
        $query->with('order');
        
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function updateOrderWarranty($order){
        try{
            DB::beginTransaction();
                $warranty = new Warranty();
                $warranty->order_id = $order['id'];
                $warranty->customer_id = !empty($order['user_id'])?$order['user_id']:1;
                $warranty->warehouse_id = $order['warehouse_id'];
                $warranty->customer_name = $order['username'];
                $warranty->customer_phone = $order['phone_number'];
                $warranty->customer_address = $order['address'];
                $warranty->customer_email = "shopsoa82@gmail.com";
                $warranty->created_by = auth()->user()->id;
                $warranty->save();
                foreach($order['details'] as $detail){
                    for($i = 1; $i <= $detail['qty']; $i++){
                        if (strlen($detail['serials'][$i-1]) >= 5){

                        }else{
                            return ["message" => "Serial " . $detail['product']->name . ": Không đúng!"];
                        }
                        $warranty_detail = new WarrantyDetail();
                        $warranty_detail->warranty_id = $warranty->id;
                        $warranty_detail->product_id = $detail['product_id'];
                        $warranty_detail->serial =  $detail['serials'][$i-1];
                        $warranty_detail->time_warranties = $detail['time_warranties'][$i-1]??24;
                        $warranty_detail->order_detail_id = $detail['id'];
                        $warranty_detail->created_by = auth()->user()->id;
                        $warranty_detail->save();
                    }
                }
            DB::commit();
            return $warranty;
        }catch(\Exception $e) {
            DB::rollBack();
            dd($e);
            return ["message" => $e];
        } 
    }
    public function updateWarrantyDetail($details){
        try{
            DB::beginTransaction();
               foreach ($details as $detail) {
                if (strlen($detail['serial']) < 5 || $detail['time_warranties'] < 1){
                    return ["message" => "Serial " . $detail['product']['name'] . " không đúng  hoặc Sai thời gian bảo hành"];
                    break;
                }
                    WarrantyDetail::where('id',$detail['id'])->update(['serial' => $detail['serial'],'time_warranties' => $detail['time_warranties']]);
               }
            DB::commit();
        }catch(\Exception $e) {
            DB::rollBack();
            dd($e);
            return ["message" => $e];
        } 
    }
   
}
