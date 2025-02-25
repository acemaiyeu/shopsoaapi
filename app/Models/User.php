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
use App\Models\Role;
use App\Models\PermissionDetail;

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
    public function role(){
        return $this->hasOne(Role::class, 'code', 'role_code')->select('code','name');
    }
    public function permission_details(){
        return $this->hasMany(PermissionDetail::class, 'user_id', 'id')->with('permission')->select('id','user_id','permission_id');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_details')->with;
    }
    public function hasPermission($permissionCode)
    {
        return $this->permission_details()->whereHas('permission', function($query) use($permissionCode) {
            $query->where('code', $permissionCode);
        })->exists();
    }
}
