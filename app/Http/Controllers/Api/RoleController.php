<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Transformers\RoleTransformer;
use App\Models\ModelsQuery\RoleModel;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $roleModel;
    public function __construct(RoleModel $modelRole) {
        $this->roleModel = $modelRole;
    }
   
    public function savePermissionDetail(Request $req){
        $role = $this->RoleModel->savePermissionUser($req);
        if (is_array($role)){
            return response(["data" => ["message" => $permission['message']]]);
        }
        return fractal($role, new RoleTransformer())->respond();
    } 
    public function saveRole(Request $req){
        $role = $this->roleModel->saveRole($req);
        if (is_array($role)){
            return response(["data" => ["message" => $permission['message']]]);
        }
        return fractal($role, new RoleTransformer())->respond();
    }   
    
}
