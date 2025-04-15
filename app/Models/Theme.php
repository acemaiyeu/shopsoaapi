<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gift;

class Theme extends Model
{
    use HasFactory;
    protected $table = 'themes';
    protected $fillable = [
        'id',
        // 'code',
        // 'name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function users(){
        return $this->hasMany(User::class);
    }

    public function gifts(){
        return $this->hasOne(Gift::class, 'id', 'gift')->with('details')->select('id','title');
    }
}