<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings, WithMapping
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

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'email'
        ];
    }
}
