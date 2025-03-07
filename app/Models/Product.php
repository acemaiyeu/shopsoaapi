<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariation;
use App\Models\WarehouseDetail;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'id',
        'name',
        'color_name',
        'price',
        'brand',
        'discount',
        'infomation_short',
        'infomation_long',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];



    public function getProductVariation(){
        return $this->hasMany(ProductVariation::class);
    }
    public function warehouse_details(){
        return $this->hasOne(WarehouseDetail::class);
    }
    public function category(){
        return $this->hasOne(Category::class, 'code', 'category_code');
    }
}
