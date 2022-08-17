<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'userName',
        'password',
        'email',
    ];
    
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    /**
     * Get the Addresses for the User.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Get the Phones for the User.
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }
    
    /**
     * Get the Orders for the User.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get all of the Products for the User.
     */
    public function Products()
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'rate',
            'comment',
        ])->as('product_user');
    }
}
