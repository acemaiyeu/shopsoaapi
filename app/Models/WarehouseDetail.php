<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Product;

class WarehouseDetail extends Model
{
    use HasFactory;
    protected $table = 'warehouse_details';
    protected $fillable = [
        'id',
        'warehouse_id',
        'product_id',
        'qty',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function warehouse(){
        return $this->belongsTo(Warehouse::class)->select('id','code','name');
    }
    public function product(){
        return $this->belongsTo(Product::class)->select('id','code','name');
    }
}
