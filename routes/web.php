<?php

use App\Http\Controllers\TransaksiPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/transaksi/download-pdf', [TransaksiPDFController::class, 'downloadPDF'])->name('transaksi.download-pdf');

