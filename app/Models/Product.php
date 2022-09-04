<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Product extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'products';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'AED',
        'USD',
        'SAR',
        'quantity',
        'imageUrl', 
        'imagePath', 
        'bannerUrl',
        'bannerPath',
        'show_in',
        'admin_id',
        'department_id',
    ];
    public $translatedAttributes = [
        'name',
        'unit',
        'description',
    ];
    
    protected $hidden = [
        'admin_id', 
        'department_id',
        'imagePath',
        'bannerPath'
    ];

    /**
     * Get the Admin that owns the Products.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get the Department that owns the Products.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get all of the Users for the Products.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot([
            'rate',
            'comment',
        ])->as('product_user');
    }
    
    /**
     * Get all of the Orders for the Products.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot([
            'price',
            'quantity',
        ])->as('product_order');
    }
    
    /**
     * Get the Product Orders for the Product.
     */
    public function product_orders()
    {
        return $this->hasMany(ProductOrder::class);
    }
    
    /**
     * Get the Quantity Adjustments for the Product.
     */
    public function quantity()
    {
        return $this->hasMany(QuantityAdjustments::class);
    }
}
