<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'cost_paid',
        'iban',
        'date_payment',
        'swift_code',
        'currency',
        'document_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
