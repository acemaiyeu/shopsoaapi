<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Theme;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_code',
        'price',
        'discount_price',
        'total_price',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    // public function cart(){
    //     return $this->hasMany(Cart::class)->with("user")->select('cart_id','product_id','qty','price','total','total_text');
    // }
    public function theme(){
        return $this->hasOne(Theme::class, 'id', 'theme_id')->select('id','code','title','thumbnail_img');
    }
    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
  
}