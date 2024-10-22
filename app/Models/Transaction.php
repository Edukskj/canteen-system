<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'student_id',
        'value',
        'type',
        'notes',
    ];

    public function guardian() {
        return $this->belongsTo(Guardian::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
