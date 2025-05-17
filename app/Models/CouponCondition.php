<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCondition extends Model
{
    use HasFactory;

    protected $table = 'coupon_conditions';

    protected $casts = [
        'condition_data' => 'object',
    ];

    protected $fillable = [
        'id',
        'coupon_id',
        'condition_apply',
        'condition_data',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function coupon()
    {
        return $this->hasMany(Coupon::class)->select('id', 'coupon_id', 'condition_apply', 'condition_data');
    }

    // public function detailsHaveProductSort(){
    //     return $this->hasMany(CartDetail::class)->with("productShort")->select('cart_id','theme_id','quantity','price','total_price','total_text');
    // }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
