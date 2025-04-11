<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Cart;
use App\Models\Order;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use App\Models\Role;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
        protected $table = "users";

     protected $fillable = [
        'name',
        'username',
        'fullname',
        'email',
        'phone',
        'password',
        'role_code',
        'role_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function cart(){
        $this->hasMany(Cart::class);
    }
    public function orders(){
        $this->belongto(Order::class);
    }
    public function findForPassport($identifier) {
        return $this->where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->first();
    }
    protected function city(){
        return $this->belongsTo(City::class);
    }
    protected function district(){
        return $this->belongsTo(District::class);
    }
    protected function ward(){
        return $this->belongsTo(Ward::class);
    }
    protected function role(){
        return $this->belongsTo(Role::class);
    }
}