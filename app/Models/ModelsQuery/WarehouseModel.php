<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseProductDetailStatus;
use App\Models\WarehouseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseModel extends Model
{

    public function getAllWarehouse($request){
        $query =  Warehouse::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['code'])){
            $query->where('code',$request['code']);
        }
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['address'])){
            $query->where('address','like', "%" .  $request['address']. "%");
        }

        if (!empty($request['product_code'])){
            $query->whereHas('details', function ($query) use($request){
                $query->whereHas('product', function($query) use ($request){
                    $query->where('code', $request['product_code']);
                });
            });
        }
        if (!empty($request['product_name'])){
            $query->whereHas('details', function ($query) use($request){
                $query->whereHas('product', function($query) use ($request){
                    $query->where('name', 'like',"%" . $request['product_name'] . "%");
                });
            });
        }

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
    public function getWarehouseDetail($request){
        $query =  WarehouseDetail::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        // if (!empty($request['id'])){
        //     $query->where('id',$request['id']);
        // }
        if (!empty($request['code'])){
            $query->where('code',$request['code']);
        }
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['product_code'])){
            $query->whereHas('product',function($query) use($req){
                    $query->where('code',$req['product_code']);
            });
        }
        if (!empty($request['product_name'])){
            $query->whereHas('product',function($query) use($req){
                $query->where('code','like',"%" . $req['product_name'] . "%");
            });
        }
        if  (!empty($request['warehouse_id'])){
            $query->where('warehouse_id', $request['warehouse_id']);
        }
        
        if (!empty($request['warehouse'])){
            $query->whereHas('warehouse', function($query) use($request){
                    $query->where('code', $request['warehouse'])->orWhere('name', 'like', '%'. $request['warehouse'] . '%');
            });
        }
        if (!empty($request['product'])){
            $query->whereHas('product', function($query) use($request){
                    $query->where('code', $request['product'])->orWhere('name', 'like', '%'. $request['product'] . '%');
            });
        }
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
    public function getWarehouseProductDetail($request){
        $query =  WarehouseProductDetailStatus::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['id'])){
            $query->where('id',$request['id']);
        }
        if (!empty($request['code'])){
            $query->where('code',$request['code']);
        }
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['address'])){
            $query->where('address','like', "%" .  $request['address']. "%");
        }
        if (!empty($request['status'])){
            $query->where('status',$request['status']);
        }
        if (!empty($request['warehouse_id'])){
            $query->whereHas('warehousedetail', function($query) use($request){
                    $query->whereHas('warehouse', function($query) use($request){
                            $query->where('id',$request['warehouse_id']);
                    });
            });
        }
        if (!empty($request['product_code'])){
            $query->whereHas('product',function($query) use($req){
                    $query->where('code',$req['product_code']);
            });
        }
        if (!empty($request['product_name'])){
            $query->whereHas('product',function($query) use($req){
                $query->where('code','like',"%" . $req['product_name'] . "%");
            });
        }
        if (!empty($request['warehouse'])){
                $query->whereHas('warehousedetail', function($query) use($request){
                    $query->whereHas('warehouse',  function($query) use($request){
                        $query->where('code',$request['warehouse'])->orwhere('name' , 'like',  "%" . $request['warehouse']. "%");
                    });
                });
        }
        if (!empty($request['product'])){
            $query->whereHas('warehousedetail', function($query) use($request){
                $query->whereHas('product',  function($query) use($request){
                    $query->where('code',$request['product'])->orwhere('name', 'like', "%" . $request['product'] . "%");
                });
            });
        }
            if (!empty($request['createdby'])){
                $query->whereHas('create', function($query) use($request){
                    $query->where('username', 'like', "%" . $request['createdby'] . "%");
                });
            }
        $query->with('warehousedetail');
        $query->with('user');
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
   public function saveWarehouseProductDetail($req){
      $warehouse =   Warehouse::whereNull('deleted_at')->find($req['warehouse_id']);
      try {
        DB::beginTransaction();
            if(!empty($warehouse)){
                    $warehouse_product_detail = new WarehouseProductDetailStatus();
                    if (!empty($req['id'])){
                        $warehouse_product_detail = WarehouseProductDetailStatus::whereNull('deleted_at')->find($req['id']);
                    }
                    $product = Product::whereNull("deleted_at")->where('code', $req['product_code'])->first();
                    if (!empty($product)){
                        $warehouse_detail = WarehouseDetail::whereNull('deleted_at')->where("warehouse_id",$warehouse->id)->where('product_id', $product->id)->first();
                    
                            
                            if (empty($warehouse_detail)){
                                $warehouse_detail =  new WarehouseDetail();
                                $warehouse_detail->qty =  $req['qty'];
                            }else{
                                $warehouse_detail->qty += $req['qty'];
                            }
                            $warehouse_detail->warehouse_id = $warehouse->id;
                            $warehouse_detail->product_id = $product->id;
                            $warehouse_detail->created_by = auth()->user()->id;
                            $warehouse_detail->save();
                            $warehouse_product_detail->warehouse_detail_id = $warehouse_detail->id;
                            $warehouse_product_detail->status = $req['status'];
                            $warehouse_product_detail->qty = $req['qty'];
                            $warehouse_product_detail->created_by = auth()->user()->id;
                            $warehouse_product_detail->save();
                    }else{
                            return response(["data" => ["message" => "Không tìm thấy sản phẩm " . $req['product']]],400);
                    }
            }else{
                return response(["data" => ["message" => "Không tìm thấy kho hàng" . $req['warehouse']]],400);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }     
      }
      public function saveWarehouse($req){
        try {
            DB::beginTransaction();
                $warehouse = new Warehouse();
                if (!empty($req['id'])){
                    $warehouse = Warehouse::whereNull('deleted_at')->find($req['id']);
                }  
                $warehouse->code = $req['code']??$warehouse->code;
                $warehouse->name = $req['name']??$warehouse->name;
                $warehouse->address = $req['address']??$warehouse->address;
                $warehouse->lat = $req['lat']??$warehouse->lat;
                $warehouse->lon = $req['lon']??$warehouse->lon;
                $warehouse->save();

            DB::commit();
            return $warehouse;
          } catch (\Exception $e) {
              DB::rollBack();
              throw $e;
          }     
        }
}
