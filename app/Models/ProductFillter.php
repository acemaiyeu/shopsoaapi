<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fillter;

class ProductFillter extends Model
{
    use HasFactory;
    protected $table = 'product_fillters';
    protected $fillable = [
        'type',
        // 'product_fillter_type',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    
    public function getFillters(){
        return $this->hasMany(Fillter::class, 'product_fillter_type', 'type');
    }
}
