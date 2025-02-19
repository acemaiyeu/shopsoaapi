<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionLogin extends Model
{


    use HasFactory;
    protected $table = 'session_logins';
    use SoftDeletes;
    protected $fillable = [
        'session',
        'user_id',
        'device',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
   
}
