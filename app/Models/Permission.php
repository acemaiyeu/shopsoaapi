<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\User;
use App\Models\PermissionDetail;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $fillable = [
        'id',
        'code',
        'name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(PermissionDetail::class, 'permission_id','id')->with("user");
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
