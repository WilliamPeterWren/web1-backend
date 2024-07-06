<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'address_id',
    ];
    protected $hidden = [
        'password',
        'remember token',
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
        return $this->hasMany('App\Models\Product');
    }

    public function userorders(){
        return $this->hasMany('App\Models\UserOrder');
    }
    
    public function cartItems()
    {
        return $this->hasMany('App\Models\ShoppingCart');
    }
    public function wishlistProducts()
    {
        return $this->hasMany('App\Models\Wishlist');
    }
    public function addresses()
    {
        return $this->hasMany('App\Models\Address');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}