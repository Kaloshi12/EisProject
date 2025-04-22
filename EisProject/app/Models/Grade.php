<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'lecture_id',
        'type',
        'weight',
        'points',
        'is_seen'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecture_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}