<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'class_type',
    ];

    /**
     * Get the department that owns the classroom.
     */
    

    /**
     * Get the faculty that owns the classroom.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
