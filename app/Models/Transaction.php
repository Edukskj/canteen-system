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

    public static function createTransaction($data)
    {
        $transaction = self::create($data);
        $transaction->afterCreate();
        return $transaction;
    }

    protected function afterCreate(): void
    {
        /** @var Transaction $transaction */
        $transaction = $this;

        $guardian = Guardian::find($transaction->guardian_id);
        
        if ($guardian){
            if ($transaction->type === 'E') {
                $guardian->adicionaSaldo($transaction->value);
            } else {
                $guardian->retiraSaldo($transaction->value);
            }
        }
    }
}
