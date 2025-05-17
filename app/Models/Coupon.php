<?php

namespace App\Models;

use App\Models\CartDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'id',
        'name',
        'code',
        'type',
        'condition',
        'data',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    public function conditions()
    {
        return $this->hasMany(CouponCondition::class, 'coupon_id', 'id');
    }
}
