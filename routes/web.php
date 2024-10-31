<?php

use App\Http\Controllers\DownloadPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin/{record}/pdf', [DownloadPdfController::class, 'download'])->name('order.pdf.download');