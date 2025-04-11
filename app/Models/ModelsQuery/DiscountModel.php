<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Discount;

class DiscountModel extends Model
{
    public function getDiscounts($request){
        $query =  Discount::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        $query->where('active', 1)->where('show',1);
        $limit = $req['limit']??30;
        $query->with('conditions');
        if ($limit == 1){
            return $query->first();
        }
       
        if ($limit > 1){
            return $query->paginate($limit);
        }   
    }
    // public function updateProfile($req){
    //     try {   
    //         $user = auth()->user();
    //         if ($user){
    //             $user->fullname = $req->fullname??$user->fullname;
    //             $user->email = $req->email??$user->email;
    //             $user->phone = $req->phone??$user->phone;
    //             $user->ward_id = $req->ward_id??$user->ward_id;
    //             $user->district_id = $req->district_id??$user->district_id;
    //             $user->city_id = $req->city_id??$user->city_id;
    //             $user->save();
    //             return $user;
    //         }else{
    //             return  ["status" => 404, "message" => "Không tìm thấy người dùng"];
    //         }
    //     }catch(Exception $e){
    //         return  ["status" => 500, "message" => $e];
    //     }
    // }
   
}