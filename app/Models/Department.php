<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;


class Department extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'departments';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'imageUrl', 
        'imagePath', 
        'admin_id', 
        'department_id'
    ];
    protected $hidden = [
        'admin_id', 
        'department_id',
        'imagePath'
    ];
    public $translatedAttributes = ['name'];
    
    /**
     * Get the Admin that owns the Department.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get the Parent that owns the Department.
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    /**
     * Get the Products for the Department.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the Children for the Department.
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'department_id');
    }
    
    /**
     * Get the Discount for the Department.
     */
    public function discount()
    {
        return $this->hasMany(DepartmentDiscount::class);
    }
}
