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
        'period',
        'delivery',
        'status'
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function payment_method() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
