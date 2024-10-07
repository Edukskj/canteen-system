<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guardian_id',
        'rm',
        'active',
    ];

    public function guardian() {
        return $this->belongsTo(Guardian::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
     }
}