<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'credits',
        'etc',
        'semester',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_course')
            ->withPivot('final_grade', 'status')
            ->withTimestamps();
    }
}
