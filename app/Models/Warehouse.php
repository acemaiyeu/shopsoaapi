<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WarehouseDetail;

class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'warehouses';
    protected $fillable = [
        'id',
        'code',
        'name',
        'lat',
        'lon',
        'address',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(WarehouseDetail::class)->select('id','warehouse_id','product_id','qty');
    }
    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
}
