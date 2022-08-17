<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admins';
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
    ];
    protected $hidden = [
        'password', 
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
     * Get the Socail Media for the Admin.
     */
    public function socailMedia()
    {
        return $this->hasMany(SocailMedia::class);
    }

    /**
     * Get the Departments for the Admin.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the Products for the Admin.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the Quantity Adjustments for the Admin.
     */
    public function quantityAdjustments()
    {
        return $this->hasMany(QuantityAdjustments::class);
    }
}
