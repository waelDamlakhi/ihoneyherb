<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BannerProduct extends Pivot
{
    use HasFactory;
    
    protected $table = 'banner_product';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_id',
        'product_id',
        'admin_id',
        'bannerUrl',
        'bannerPath'
    ];
    
    protected $hidden = [
        'admin_id', 
        'product_id',
        'banner_id',
        'bannerPath'
    ];

    /**
     * Get the Admin that owns the Product Banner.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the Admin that owns the Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the Admin that owns the Banner.
     */
    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
