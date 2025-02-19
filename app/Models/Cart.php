<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = [
        'id',
        'user_id',
        'session',
        'payment',
        'discount_code',
        'discount_price',
        'note',
        'address',
        'promo_code',
        'total_price',
        'total_pay',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(CartDetail::class)->with("product")->select('id','cart_id','product_id','qty','price','total','total_text','discount_price','discount_code','discount_name');
    }
    public function detailsHaveProductSort(){
        return $this->hasMany(CartDetail::class)->with("productShort")->select('cart_id','product_id','qty','price','total','total_text');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
