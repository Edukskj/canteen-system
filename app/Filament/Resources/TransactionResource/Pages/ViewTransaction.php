<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        if ($this->record->type !== 'R') {
            return [
                Actions\Action::make('runReversal')
                    ->label('Estornar')
                    ->action(function () {
                        $transaction = $this->record;
                        
                        if ($transaction->type === 'S') {
                            $reversalType = 'E';
                        } else {
                            $reversalType = 'S';
                        };
    
                        $tran = Transaction::find($transaction->id);
                        $tran->reversal($reversalType);
    
                    })
                    ->requiresConfirmation()
                    ->color('info')
            ];
        } else {
            return [
                Actions\Action::make('estornado')
                    ->label('Estornado')
                    ->disabled()
                    ->color('gray')
            ];
        }
    }
}
