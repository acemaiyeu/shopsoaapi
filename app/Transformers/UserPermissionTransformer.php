<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Warranty;
class UserPermissionTransformer extends TransformerAbstract
{
    
    public function transform(User $user)
    {
        return [
            'id'               => $user->id,
            'username'         => $user->username,
            'phone_number'      => $user->phone??"",
            'role'              =>  $user->role,
            'permission_details' => $user->permission_details??[],
            'created_at'       => Carbon::parse($user->created_at)->format('d-m-Y')
        ];
    }
}
