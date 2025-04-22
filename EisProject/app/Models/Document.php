<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'cost',
    ];

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
}
