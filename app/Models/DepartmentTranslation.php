<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentTranslation extends Model
{
    use HasFactory;

    protected $table = 'department_translations';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'locale', 
        'department_id'
    ];
    protected $hidden = [
        'department_id', 
    ];
}
