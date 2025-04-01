<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $fillable = [
        'name',
    ];

  
    /**
     * Get the degree that owns the department.
     */
    public function degree()
    {
        return $this->hasMany(Degree::class);
    }

    /**
     * Get the faculty that owns the department.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
