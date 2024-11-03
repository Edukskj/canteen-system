<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'active',
        'wallet',
    ];

    public function students() {
        return $this->hasMany(Student::class);
    }

    public function transações() {
        return $this->hasMany(Transaction::class);
    }

    public function adicionaSaldo($valor)
    {
        $this->wallet += $valor;
        $this->save();
    }

    public function retiraSaldo($valor)
    {
        $this->wallet -= $valor;
        $this->save();
    }

}
