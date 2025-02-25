<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Role;
use Akaunting\Money\Money;

class RoleTransformer extends TransformerAbstract
{
    
    public function transform(Role $role)
    {
        return [
            'code' => $role->code,
            'name' => $role->name,
            'created_at'  => $role->created_at,
        ];
    }
}
