<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Unit extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'units';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'admin_id'
    ];
    protected $hidden = ['admin_id'];
    public $translatedAttributes = ['name'];

    /**
     * Get the products for the unit.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the Admin that owns the Unit.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
