<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductVariation extends Model
{
    use HasFactory;
    // 'biến thể product'
    protected $table = "product_variation";
    protected $fillable = [
        'product_id',
        'color_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function product(){
        return $this->hasOne(Product::class, 'product_id','id');
    }
}
