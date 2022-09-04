<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuantityAdjustments extends Model
{
    use HasFactory;

    protected $table = 'quantity_adjustments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'operation_type',
        'description',
        'product_id',
        'admin_id',
    ];
    
    protected $hidden = [
        'product_id',
        'admin_id'
    ];

    /**
     * Get the Admin that owns the Quantity Adjustments.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get the Product that owns the Quantity Adjustments.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
