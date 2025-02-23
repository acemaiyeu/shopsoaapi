<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\User;
class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'code',
        'name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function products(){
        return $this->hasMany(Product::class, 'category_code', 'code');
    }
    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
