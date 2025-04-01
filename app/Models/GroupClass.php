<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupClass extends Model
{   
    protected $fillable = [
        'name',
        'nr_max_student',
        'year_study',
        'degree_id',
    ];
    /**
     * Get the degree that owns the group class.
     */
    public function degree()
    {
        return $this->hasMany(Degree::class);
    }
}
