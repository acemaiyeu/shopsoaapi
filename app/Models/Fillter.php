<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductFillter;

class Fillter extends Model
{
    use HasFactory;
    protected $table = 'fillters';
    protected $fillable = [
        'id',
        'property',
        'value',
        'product_fillter_type',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function getProductFillter(){
        return $this->belongsTo(ProductFillter::class, 'type', 'product_fillter_type');
    }
}
