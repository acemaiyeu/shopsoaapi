<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cart_details';

    protected $fillable = [
        'id',
        'cart_id',
        'product_id',
        'qty',
        'price',
        'discount',
        'discount_code',
        'discount_price',
        'total',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class)->select('id', 'title', 'thumbnail_img', 'short_description', 'price', 'price_text');
    }

    public function productShort()
    {
        return $this->belongsTo(Product::class)->select('id', 'code', 'name', 'price', 'price_text');
    }
}
