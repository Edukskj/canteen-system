<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{

    use Exportable;

    public function __construct( public Collection $records) {
        // $this->records = $records;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->records;
    }

    public function map($transaction): array
    {
        return [
            $transaction->student_id,
            $transaction->guardian_id,
            $transaction->value,
            $transaction->notes,
            $transaction->type,
        ];
    }

    public function headings(): array
    {
        return [
            'Aluno ID',
            'Resp. ID',
            'Valor',
            'Observação',
            'Tipo'
        ];
    }
}
