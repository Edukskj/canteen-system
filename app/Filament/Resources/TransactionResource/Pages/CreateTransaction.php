<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use App\Models\Transaction; // Importar o modelo Transaction
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        /** @var Transaction $transaction */
        $transaction = $this->record;

        // Chama o método do modelo para processar a transação
        $transaction->processTransaction(); // Certifique-se que está chamando do objeto Transaction
    }

}

