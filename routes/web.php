<?php

use App\Filament\Resources\TransaksiResource;
use App\Http\Controllers\TransaksiPDFController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/transaksi/export-pdf', [TransaksiResource::class, 'exportToPdf'])->name('transaksi.export-pdf');
Route::redirect('/', '/admin');
