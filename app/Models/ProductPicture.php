<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPicture extends Model
{
    use HasFactory;
    
    protected $table = 'product_pictures';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'imageUrl', 
        'imagePath',
        'product_id',
    ];
    
    protected $hidden = [
        'imagePath',
        'product_id'
    ];
    
    /**
     * Get the Product that owns the Pictures.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
