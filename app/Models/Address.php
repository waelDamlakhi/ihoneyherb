<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'type',
        'user_id'
    ];
    
    /**
     * Get the Addresses that owns the User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
