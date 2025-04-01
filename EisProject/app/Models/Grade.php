<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'type',
        'weight',
        'points',
    ];

    /**
     * Get the user that owns the grade.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the grade.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
