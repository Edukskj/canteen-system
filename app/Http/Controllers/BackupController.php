<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{

    public function runBackup()
    {
        // Chama o comando artisan de backup
        Artisan::call('backup:run');

        Notification::make()
        ->title('Backup realizado com sucesso!')
        ->success()
        ->send();

        // Opcionalmente, você pode obter a saída do comando
        $output = Artisan::output();

        // Redireciona ou retorna uma resposta
        return redirect()->back()->with('success', 'Backup realizado com sucesso!')->with('output', $output);
    }
    
}