<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'social_media';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'info',
        'imageUrl', 
        'imagePath', 
        'admin_id',
    ];
    protected $hidden = [
        'admin_id',
        'imagePath'
    ];

    /**
     * Get the Admin that owns the Social Media.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
