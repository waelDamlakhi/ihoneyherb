<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentDiscount extends Model
{
    use HasFactory;

    protected $table = 'department_discounts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start',
        'end',
        'discount', 
        'department_id', 
        'admin_id',
    ];
    protected $hidden = [
        'admin_id',
        'department_id'
    ];

    
    /**
     * Get the Admin that owns the Department Discount.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    
    /**
     * Get the Department that owns the Department Discount.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
