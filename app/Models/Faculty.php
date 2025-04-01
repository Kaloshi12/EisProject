<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the department that owns the faculty.
     */
    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    
}
