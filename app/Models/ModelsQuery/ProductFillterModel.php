<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\ProductFillter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Fillter;

class ProductFillterModel extends Model
{

    public function getAllFillter($request){
        $query =  ProductFillter::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['type'])){
            $query->where('type', 'like', "%" . $request['type'] . "%");
        }
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['created_by'])){
            $query->whereHas('createdBy', function($query) use($request){
                $query->where('username', 'like', "%". $request['created_by'] . "%");
            });
        }
        $query->with('createdBy');        
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function saveFillter($req){
        try {
            DB::beginTransaction();
            
            $fillter = new ProductFillter();
            if (!empty($req['id'])){
                $fillter  = ProductFillter::whereNull('deleted_at')->find($req['id']);
                $fillter->updated_at = auth()->user()->id;
            }
            $fillter->type = $req['type']??$fillter->type;
            if (empty($fillter->created_by)){
                $fillter->created_by = auth()->user()->id;
            }
            $fillter->save();
            FIllter::whereNull('deleted_at')->where('product_fillter_type' , $fillter->type)->update(['deleted_at' => Carbon::now()]);
            foreach($req['details'] as $detail){
                    $new_detail = new Fillter();
                    $new_detail->id = $detail->id??null;
                    $new_detail->property =  $detail['property'];
                    $new_detail->property_name =  $detail['property_name'];
                    $new_detail->value =   json_encode($detail['value']);
                    $new_detail->product_fillter_type = $fillter->type;
                    if (empty($detail['created_by'])){
                       $new_detail->created_by = auth()->user()->id; 
                    }
                    $new_detail->save();
            }
            DB::commit();
            return $fillter;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["message" => $e];
        }
    }
}
