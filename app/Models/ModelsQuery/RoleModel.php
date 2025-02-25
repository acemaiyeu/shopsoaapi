<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleModel extends Model
{

    public function getAllRole($request){
        $query =  Role::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['code'])){
            $query->where('code', $request['code']);
        } 
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function saveRole($request){
        try{
            DB::beginTransaction();
                $role = new Role();
                $check = Role::whereNull('deleted_at')->where('code', $request['code'])->exists();
            if ($check){
                    return ["message" => "code đã tồn tại"];
            }
            if (!empty($request['code'])){
                $role = Role::whereNull('deleted_at')->where('code', $request['code'])->first();
            }
                $role->code = $request['code'];
                $role->name = $request['name'];
                $role->created_by = auth()->user()->id;
                $role->save();
            DB::commit();
            return $role;
        }catch(\Exception $e) {
            DB::rollBack();
        //   dd($e);
            return ["data" => ["message" => $e]];
        }
    }
    
}
