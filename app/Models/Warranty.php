<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WarehouseDetail;

class Warranty extends Model
{
    use HasFactory;
    protected $table = 'warranties';
    protected $fillable = [
        'id',
        'warehouse_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(WarrantyDetail::class)->with('product')->select('id','warehouse_id','product_id','qty');
    }
    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
}
