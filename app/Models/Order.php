<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'tel',
        'statue',
        'currency',
        'user_id'
    ];
    
    /**
     * Get the Orders that owns the User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get all of the Products for the Orders.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'price',
            'quantity',
        ])->as('product_order');
    }
}
