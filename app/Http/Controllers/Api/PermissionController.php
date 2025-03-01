<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\PermissionModel;
use App\Http\Requests\RegisterValidator;
use Illuminate\Support\Facades\Response;
use App\Transformers\PermissionTransformer;
use App\Transformers\UserPermissionTransformer;
use App\Transformers\RoleTransformer;
use App\Models\ModelsQuery\RoleModel;
use App\Models\PermissionDetail;
use Carbon\Carbon;
class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $permissionModel;
    protected $roleModel;
    public function __construct(PermissionModel $model, RoleModel $modelRole) {
        $this->permissionModel = $model;
        $this->roleModel = $modelRole;
    }
    public function getPermission(Request $req){
        $permissions = $this->permissionModel->getPermission($req);
        return fractal($permissions, new PermissionTransformer())->respond();
    }
    public function savePermissionDetail(Request $req){
        $permission = $this->permissionModel->savePermissionUser($req);
        if (is_array($permission)){
            return response(["message" => $permission['message']]);
        }
        return response(["message" => "Đã lưu thành công!"]);
    }
    public function savePermission(Request $req){
        // dd($req->all());
        $permission = $this->permissionModel->savePermission($req);
        if (is_array($permission)){
            return response(["data" => ["message" => $permission['message']]]);
        }
        return fractal($permission, new PermissionTransformer())->respond();
    }
    
    public function getUserPermission(Request $req){
        $users = $this->permissionModel->getUserPermission($req);
        return fractal($users, new UserPermissionTransformer())->respond();
    }
    public function getAllRole(Request $req){
        $users = $this->roleModel->getAllRole($req);
        return fractal($users, new RoleTransformer())->respond();
    }
    public function deletePermissionDetailById($id){
        PermissionDetail::whereNull('deleted_at')->update(['deleted_at' => Carbon::now('Asia/Ho_Chi_Minh'), "deleted_by" => auth()->user()->id]);
        return response(["data" => ["message" => "Đã xóa thành công!"]],200);
    }
    
    
}
