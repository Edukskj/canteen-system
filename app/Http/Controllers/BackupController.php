<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{

    public function runBackup()
{
    try {
        $output = shell_exec('php artisan backup:run 2>&1'); // Executa o comando e captura a saída
        Log::info('Saída do backup: ' . $output);

        return redirect()->back()->with('success', 'Backup realizado com sucesso!');
    } catch (\Exception $e) {
        Log::error('Erro ao realizar o backup: ' . $e->getMessage());

        return redirect()->back()->with('error', 'Erro ao realizar o backup: ' . $e->getMessage());
    }
}

    
}