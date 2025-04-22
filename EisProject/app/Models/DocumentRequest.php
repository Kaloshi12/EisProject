<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DocumentRequest extends Model
{
    protected $fillable = [
        'student_id',
        'document_id',
        'status',
        'requested_at'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}

