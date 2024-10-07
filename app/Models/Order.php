<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'notes',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
