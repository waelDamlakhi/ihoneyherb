<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    
    protected $table = 'banners';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'count',
    ];

    /**
     * Get all of the Products for the Banner.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'bannerUrl',
            // 'bannerPath',
        ])->as('banner_product');
    }
}
