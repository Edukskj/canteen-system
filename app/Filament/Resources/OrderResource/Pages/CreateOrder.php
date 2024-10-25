<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        /** @var Order $order */
        $order = $this->record;

        $student = Student::find($order->student_id);
        if ($student){
            $student->adicionaSaldoRepres($order->grand_total);
        }

    }
}
