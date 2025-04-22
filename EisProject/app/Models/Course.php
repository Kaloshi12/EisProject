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
        'category',
        'semester',
        'degree_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'status', 'final_grade', 'academic_year', 'is_active', 'class_group_id')
            ->withTimestamps();
    }

    public function lecturers()
    {
        return $this->users()->wherePivot('role', 'lecture');
    }

    public function assistants()
    {
        return $this->users()->wherePivot('role', 'assistant');
    }

    public function students()
    {
        return $this->users()->wherePivot('role', 'student');
    }

    public function hours()
    {
        return $this->hasMany(CourseHours::class);
    }

    public function studentClassGroups()
    {
        return $this->students()
            ->select('users.id', 'name', 'pivot.class_group_id')
            ->get()
            ->pluck('pivot.class_group_id')
            ->unique();
    }
}
