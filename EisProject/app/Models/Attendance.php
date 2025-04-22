<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'lecturer_id',
        'course_id',
        'student_id',
        'topic',
        'category',
        'hours_attended',
        'number_hours',
        'week',
        'date'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
