<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;

class PromotionDetail extends Model
{
    use HasFactory;
    protected $table = 'promotion_details';
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
    public function promotion(){
        return $this->belongsTo(Promotion::class)->whereNull('deleted_at')->select('id','promotion_code','promotion_name','start_time','end_time','created_at','created_by');
    }
}
