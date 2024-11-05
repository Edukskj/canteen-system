<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\DownloadPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/index-en', function () {
    return view('english');
});

Route::get('admin/pdf', [DownloadPdfController::class, 'download'])->name('order.pdf.download');
Route::post('admin/backup/run', [BackupController::class, 'runBackup'])->name('backup.run');