<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    public function getAllUsers($request){
        $query =  User::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        $limit = $req['limit']??10;
        if ($limit == 1){
            return $query->first();
        }
        if ($limit > 1){
            return $query->paginate($limit);
        }   
    }
   public function changePassword($req){
    try{
        DB::beginTransaction();
            $user = User::whereNull('deleted_at')->find($req['id']);
            if(empty($user)){
                return ['message' => "Không tìm thấy tài khoản!"];
            }
            if (!Hash::check($req['password_old'], $user->password) && $user->id != auth()->user()->id){
                return ['message' => "Mật khẩu không đúng với tài khoản!"];
            }
            if ($req['password_new'] != $req['password_confirm']){
                return ['message' => "Mật khẩu lặp lại không trùng với mật khẩu mới!"];
            }
            $user->password = Hash::make($req['password_new']);
            $user->save();
            
        DB::commit();
        return $user;
    }catch(\Exception $e) {
        DB::rollBack();
    //   dd($e);
        return ["data" => ["message" => $e]];
    }
   }
   public function createUserByAdmin($req){
    try{
        DB::beginTransaction();
            $user = User::whereNull('deleted_at')->where('email', $req['email'])->exists();
            if(!empty($user)){
                return ['message' => "Tài khoản(email) đã tồn tại!"];
            }
            if (empty($req['username'])){
                return ['message' => "Vui lòng nhập Tên tài khoản!"];
            }
            if (strlen($req['password']) < 5){
                return ['message' => "Mật khẩu phải từ 5 kí tự trở lên!"];
            }
            $user = new User();
            $user->username = $req['username'];
            $user->password = Hash::make($req['password']);
            $user->role_code = $req['role_code'];
            $user->save();
            
        DB::commit();
        return $user;
    }catch(\Exception $e) {
        DB::rollBack();
    //   dd($e);
        return ["data" => ["message" => $e]];
    }
   }
   
}
