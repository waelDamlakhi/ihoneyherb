<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductOrder extends Pivot
{
    use HasFactory;

    protected $table = 'product_order';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'order_id',
        'price',
        'quantity'
    ];
    
    protected $hidden = [
        'product_id',
        'order_id'
    ];
    
    /**
     * Get the Product that owns the Product Orders.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the Order that owns the Products Orders.
     */
    public function order()
    {
        return $this->belongsTo(Product::class);
    }
}
