<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'phone_no',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function updatePassword($user, $new_password)
    {
        $user->password = bcrypt($new_password);
        $user->save();
        return $user;
    }

    public static function getUserByEmail($email) {
        $user = User::where('email',$email)->first();
        return $user;
    }

    public function userVerification($currentUserId)
    {
        $userId = User::select('id')->where([['role', '=', 'user'], ['id', '=', $currentUserId]])->get();
        return $userId;
    }

    public function wishlists()
    {
        return $this->hasMany('App\Models\Wishlist');
    }

    public function carts()
    {
        return $this->hasmany('App\Models\Cart');
    }
    public function addresses()
    {
        return $this->hasMany('App\Models\Address');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

}
