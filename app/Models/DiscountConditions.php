<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\User;

class DiscountConditions extends Model
{
    use HasFactory;
    protected $table = 'discount_conditions';
    protected $casts = [
        'condition_data' => 'object',
    ];
    protected $fillable = [
        'id',
        'discount_id',
        'condition_apply',
        'condition_data',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function conditions(){
        return $this->hasMany(DiscountConditions::class)->select('id','discount_id','condition_apply','condition_data');
    }
    // public function detailsHaveProductSort(){
    //     return $this->hasMany(CartDetail::class)->with("productShort")->select('cart_id','theme_id','quantity','price','total_price','total_text');
    // }
    public function user(){
        return $this->belongsTo(User::class);
    }
}