<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use App\Models\PermissionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PermissionModel extends Model
{

    public function getPermission($request){
        $query =  Permission::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['code'])){
            $query->where('code', $request['code']);
        }
        if (!empty($request['createdby'])){
            $query->whereHas('createdBy', function($query) use($request){
                $query->where('name', 'like', "%". $request['createdby'] . "%");
            });
        }
        $query->with('createdBy');   
        $query->with('details');        
        $limit = $request['limit'] ?? 10000;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function getUserPermission($request){
        $query =  User::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['username'])){
            $query->where('username', 'like', "%" . $request['username'] . "%");
        }
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['phone'])){
            $query->where('phone', $request['phone']);
        }
        if (!empty($request['email'])){
            $query->where('email', $request['email']);
        }
        if (!empty($request['createdby'])){
            $query->whereHas('createdBy', function($query) use($request){
                $query->where('name', 'like', "%". $request['createdby'] . "%");
            });
        }    
        $query->whereHas('role', function($query) use($request){
                $query->where('code', '!=', 'SUPERADMIN');
        });
        if(!empty($request['role_name'])){
            $query->whereHas('role', function($query) use($request){
                $query->where('name', 'like',  "%" . $request['role_name'] . "%");
            });
        }
        if (empty($request['username']) && empty($request['phone'] && empty($request['email']) && empty($request['role']))){
            $query->orwhere('id', auth()->user()->id);
        }
        
        $query->with('role');
        $query->with('permission_details');
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }

    
    public function savePermissionUser($req){
        try {
            DB::beginTransaction();
            
            $permission = new PermissionDetail();
            $check = PermissionDetail::whereNull("deleted_at")->where('user_id', $req['user_id'])->where('permission_id', $req['permission_id'])->exists();
            if ($check){
                return ["message" => "Tài khoản đã có quyền này!"];
            }
            $permission->user_id = $req['user_id'];
            $permission->permission_id = $req['permission_id'];
            $permission->created_by = auth()->user()->id;
            $permission->save();
            DB::commit();
            return $permission;
        } catch (\Exception $e) {
            DB::rollBack();
            return ["message" => $e];
        }
    }
    public function savePermission($req){
    try {
        DB::beginTransaction();
        
        $permission = new Permission();
        $check_exist = Permission::whereNull('deleted_at')->where('code', $req['code'])->exists();
        if ($check_exist){
            return ["message" => "Mã đã tồn tại."];
        }
        if (!empty($req['id'])){
            $permission  = Permission::whereNull('deleted_at')->find($req['id']);
            $post->updated_at = auth()->user()->id;
        }
        $permission->code = $req['code'];
        $permission->name = $req['name'];
        if(empty($permission->id)){
            $permission->created_by = auth()->user()->id;
        }
        $permission->save();
        DB::commit();
        return $permission;
    } catch (\Exception $e) {
        DB::rollBack();
        return ["message" => $e];
    }
}
    // public function deleteById($id){
    //     try {
    //         DB::beginTransaction();
            
    //         $post = new Post();
    //         if (!empty($req['id'])){
    //             $post  = Post::whereNull('deleted_at')->find($req['id']);
    //         }
    //         $post->deleted_by = Carbon::now();
    //         $post->deleted_by = auth()->user()->id;
    //         $post->save();
    //         DB::commit();
    //         return $post;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $e;
    //     }
    // }
    // public function saveComment($id, $comment){
    //     try {
    //         DB::beginTransaction();
    //             $post  = Post::whereNull('deleted_at')->find($id);
    
    //             $comments = json_decode($post->comments)??[];
    //             $user = !empty(auth()->user())?auth()->user()->username:"Không đăng nhập";
    //             $comments[] = [
    //                 "username" => $user,
    //                 "comment" => $comment
    //             ];
    //             $post->comments = json_encode($comments);
    //             $post->save();
    //         DB::commit();
    //         return $post;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $e;
    //     }
    // }
   
}
