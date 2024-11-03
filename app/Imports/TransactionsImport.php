<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsImport implements ToModel, WithHeadingRow
{

    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $transaction = Transaction::create([
            'student_id'  => $row['aluno_id'],
            'guardian_id' => $row['resp_id'],
            'value' => $row['valor'],
            'notes' => $row['observacao'],
            'type' => $row['tipo'],
        ]);

        $transaction->processTransaction();

        return $transaction;
    }

}
