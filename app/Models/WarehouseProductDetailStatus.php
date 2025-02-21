<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WarehouseDetail;
use App\Models\User;
class WarehouseProductDetailStatus extends Model
{
    use HasFactory;
    protected $table = 'warehouse_product_detail_status';
    protected $fillable = [
        'id',
        'warehouse_detail_id',
        'status',
        'qty',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function warehousedetail(){
        return $this->hasOne(WarehouseDetail::class, "id","warehouse_detail_id")->with('product')->with('warehouse')->select('id','warehouse_id','product_id');
    }
    public function user(){
        return $this->hasOne(User::class, "id","created_by")->select('username');
    }
    public function create(){
        return $this->hasOne(User::class, "id","created_by")->select('username');
    }
    
}
