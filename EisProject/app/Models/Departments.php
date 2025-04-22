<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $table = 'departments';
    protected $fillable = [
        'name',
        'faculty_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    public function faculties()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'id');
    }
}