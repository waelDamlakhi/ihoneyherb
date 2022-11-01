<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    use HasFactory;
    
    protected $table = 'unit_translations';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'locale', 
        'unit_id'
    ];
    protected $hidden = [
        'unit_id', 
    ];
}
