<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'value',
        'payment_method',
        'notes',
    ];

    public function guardian() {
        return $this->belongsTo(Guardian::class);
    }
}