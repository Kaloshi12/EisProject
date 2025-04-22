<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseHours extends Model
{
    protected $table = 'course_hours';

    protected $fillable = [
        'course_id',
        'day',
        'start_hour',
        'num_hours',
        'category',
        'class_group_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }
}
