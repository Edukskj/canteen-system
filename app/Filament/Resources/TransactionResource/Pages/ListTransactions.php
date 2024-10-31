<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Imports\TransactionsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importUsers')
            -> label('Importar')
            -> color('danger')
            -> modal('importUsers')
            -> icon('heroicon-o-document-arrow-down')
            -> form([
                FileUpload::make('attachment')
            ])
            -> action(function(array $data){
                $file = Storage::disk('public')->path($data['attachment']);

                Excel::import(new TransactionsImport, $file);

                Notification::make()
                ->title('Movimentações Importadas!')
                ->success()
                ->send();
            }),

            Actions\CreateAction::make()
        ];
    }
}
