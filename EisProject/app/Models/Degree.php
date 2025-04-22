<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $table = 'degrees';
    protected $fillable = [
        'name',
        'department_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'degree_id', 'id');
    }

    public function departments()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'id');
    }   
    public function payment(){
        return $this->hasMany(Payment::class);
    }
}