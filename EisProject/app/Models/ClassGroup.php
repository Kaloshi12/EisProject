<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{

    protected $table = 'class_groups';
    protected $fillable = [
        'name',
        'degree_id',
        'department_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'class_group_id', 'id');
    }

    public function degrees()
    {
        return $this->belongsTo(Degree::class, 'degree_id', 'id');
    }
    public function courseHours()
    {
        return $this->hasMany(CourseHours::class);
    }
    public function studentGroup()
    {
        return $this->users()->wherePivot('role', 'student');
    }
}
