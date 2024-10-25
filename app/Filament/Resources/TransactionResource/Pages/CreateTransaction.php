<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use App\Models\Guardian;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        /** @var Transaction $order */
        $transaction = $this->record;

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
