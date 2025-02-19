<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;
use Akaunting\Money\Money;
use Carbon\Carbon;
class userTransformer extends TransformerAbstract
{
    
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'username'      => $user->username,
            'role_code'     => $user->role_code,
            'avatar'        => $user->avatar,
            'created_at'    => Carbon::parse($user->created_at)->format('d-m-Y')
        ];
    }
}
