<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculties';
    protected $fillable = [
        'name',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'faculty_id', 'id');
    }
    public function departments()
    {
        return $this->hasMany(Departments::class, 'faculty_id', 'id');
    }
}