<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $fillable = [
        'name',
        'degree_type',
    ];

    /**
     * Get the department that owns the degree.
     */
    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    /**
     * Get the user that owns the degree.
     */
    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function groupClass()
    {
        return $this->hasMany(GroupClass::class);
    }
}
