<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warranty;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\User;

class WarrantyDetail extends Model
{
    use HasFactory;
    protected $table = 'warranty_details';
    protected $fillable = [
        'id',
        'warranty_id',
        'product_id',
        'order_detail_id',
        'serial',
        'time_warranties',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function warranties(){
        return $this->belongsTo(Warranty::class)->select('id','code','name');
    }
    public function product(){
        return $this->belongsTo(Product::class)->select('id','code','name');
    }
    public function orderDetail(){
        return $this->belongsTo(OrderDetail::class)->select('id','product_id','price','price_text');
    }
    public function createdby(){
        return $this->hasOne(User::class,'id',"created_by")->select('id','username','phone','address');
    }

    
}
