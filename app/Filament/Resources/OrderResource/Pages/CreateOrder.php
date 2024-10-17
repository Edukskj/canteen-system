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

        //dd($order);

        $student = Student::find($order->student_id);
        //dd(get_class_methods($student));
        if ($student){
            $student->adicionaSaldoRepres($order->grand_total);
        }
        // $start =0;
        // when($start,fn($query)=> $query->whereDate('created_at','>',$start));     

    }
}
