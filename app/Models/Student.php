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
        'infantil',
        'teacher',
        'grade',
        'period',
        'observation'
    ];

    public function guardian() {
        return $this->belongsTo(Guardian::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function adicionaSaldoRepres($valor) {
        $data = [
            'guardian_id' => $this->guardian_id,
            'student_id' => $this->id,
            'value' => $valor,
            'type' => 'S',
            'notes' => 'Movimentação criada automaticamente pelo Pedido de Venda'
        ];
        
        Transaction::createTransaction($data);
    }
}