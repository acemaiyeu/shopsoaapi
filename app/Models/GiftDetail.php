<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\User;

class GiftDetail extends Model
{
    use HasFactory;
    protected $table = 'gift_details';
    protected $fillable = [
        'id',
        'user_id',
        'session',
        'payment',
        'discount_code',
        'discount_price',
        'note',
        'user_address',
        'user_phone',
        'user_email',
        'fullname',
        'info_payment',
        'total_price',
        'total_pay',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(GiftDetail::class);//->select('id','cart_id','theme_id','quantity','price','total_price','total_text','discount_price','discount_code');
    }
    // public function detailsHaveProductSort(){
    //     return $this->hasMany(CartDetail::class)->with("productShort")->select('cart_id','theme_id','quantity','price','total_price','total_text');
    // }
    public function user(){
        return $this->belongsTo(User::class);
    }
}