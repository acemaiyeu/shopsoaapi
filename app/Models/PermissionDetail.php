<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\User;
use App\Models\Permission;

class PermissionDetail extends Model
{
    use HasFactory;
    protected $table = 'permission_details';
    protected $fillable = [
        'id',
        'user_id',
        'permission_id',
        'active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function permission(){
        return $this->hasOne(Permission::class, 'id', 'permission_id')->select('id','code','name','created_at');
    }
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id')->select('id','username');
    }
    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by')->select('id','username');
    }
    // public function detailsHaveProductSort(){
    //     return $this->hasMany(CartDetail::class)->with("productShort")->select('cart_id','product_id','qty','price','total','total_text');
    // }
    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
}
