<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\PermissionModel;
use App\Http\Requests\RegisterValidator;
use Illuminate\Support\Facades\Response;
use App\Transformers\PermissionTransformer;
use App\Transformers\UserPermissionTransformer;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $permissionModel;
    public function __construct(PermissionModel $model) {
        $this->permissionModel = $model;
    }
    public function getPermission(Request $req){
        $permissions = $this->permissionModel->getPermission($req);
        return fractal($permissions, new PermissionTransformer())->respond();
    }
    public function savePermission(Request $req){
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
}
