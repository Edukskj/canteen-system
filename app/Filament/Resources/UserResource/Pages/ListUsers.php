<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Imports\UsersImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importUsers')
            -> label('Importar')
            -> color('info')
            -> modal('importUsers')
            -> icon('heroicon-o-document-arrow-down')
            -> form([
                FileUpload::make('attachment')
            ])
            -> action(function(array $data){
                $file = public_path('storage/' . $data['attachment']);

                Excel::import(new UsersImport, $file);

                Notification::make()
                ->title('Usuários Importados!')
                ->success()
                ->send();
            }),

            Actions\CreateAction::make()
        ];
    }
}
