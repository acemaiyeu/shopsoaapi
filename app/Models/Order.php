<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
use App\Models\User;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'id',
        'cart_id',
        'user_id',
        'total_price',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    // public function cart(){
    //     return $this->hasMany(Cart::class)->with("user")->select('cart_id','product_id','qty','price','total','total_text');
    // }
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id')->select('id','username');
    }
    public function details(){
        return $this->hasMany(OrderDetail::class, 'order_id', 'id')->with('product')->select('id','order_id','theme_id','price','quantity','total_price','price_text','total_price_text');
    }
}