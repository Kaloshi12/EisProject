<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the user that owns the role.
     */
    public function user()
    {
        return $this->hasMany(User::class);
    }

    
}
