<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $table = 'Phones';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tel',
        'type',
        'user_id'
    ];
    
    /**
     * Get the Phones that owns the User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
