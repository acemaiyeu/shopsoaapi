<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Permission;
use Carbon\Carbon;
use App\Models\Warranty;
class PermissionTransformer extends TransformerAbstract
{
    
    public function transform(Permission $permission)
    {
        return [
            'id'               => $permission->id,
            'code'             => $permission->code,
            'name'          => $permission->name,
            'details'           => $permission->details??[],
            'created_at'       => Carbon::parse($permission->created_at)->format('d-m-Y')
        ];
    }
}
