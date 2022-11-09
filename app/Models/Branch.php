<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    
    protected $table = 'branches';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id', 
        'address', 
        'admin_id',
    ];
    protected $hidden = [
        'admin_id', 
        'branch_id',
    ];
    
    /**
     * Get the Admin that owns the Branch.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get the Parent that owns the Branch.
     */
    public function parent()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
    /**
     * Get the Children for the Branch.
     */
    public function children()
    {
        return $this->hasMany(Branch::class, 'branch_id');
    }
}
