<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductVarian extends Model
{
    use HasFactory;
    protected $table = 'product_varians';
    protected $fillable = [
        'id',
        'product_id',
        'description',
        'price',
        'datas',
        'image_url',
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
    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
