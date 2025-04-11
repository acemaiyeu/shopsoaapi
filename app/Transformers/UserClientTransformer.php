<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\user;
use Carbon\Carbon;

class UserClientTransformer extends TransformerAbstract
{
    
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone' => $user->phone,
            'city' => $user->city->name ?? null,
            'district' => $user->district->name ?? null,
            'ward' => $user->ward->name ?? null,
            'role' => $user->role->name,
            'created_at'  => Carbon::parse($user->created_at)->format('d/m/Y H:i'),
        ];
    }
}