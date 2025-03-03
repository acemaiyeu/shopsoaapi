<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PromotionDetail;
use App\Models\User;

class Promotion extends Model
{
    use HasFactory;
    protected $table = 'promotions';
    protected $fillable = [
        'id',
        'promotion_code',
        'promotion_name',
        'start_time',
        'end_time',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function details(){
        return $this->hasMany(PromotionDetail::class)->whereNull('deleted_at')->select('type','condition','condition_data','data');
    }
    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by')->select('id','username');
    }
}
