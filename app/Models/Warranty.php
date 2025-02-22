<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WarrantyDetail;
use App\Models\User;
use App\Models\Order;

class Warranty extends Model
{
    use HasFactory;
    protected $table = 'warranties';
    protected $fillable = [
        'id',
        'warehouse_id',
        'order_id',
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
        return $this->hasMany(WarrantyDetail::class)->with('product')->with('orderDetail')->with('createdby')->select('id','warranty_id','product_id','serial','time_warranties','order_detail_id','created_at','created_by');
    }
    public function createdby(){
        return $this->hasOne(User::class,'id',"created_by")->select('id','username','phone','address');
    }
    public function order(){
        return $this->hasOne(Order::class,'id',"order_id")->select('id','total_price');
    }
    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
}
