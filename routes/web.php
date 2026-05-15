<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('sales.index');
});

Auth::routes(['reset' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    Route::get('sales/{sale}/pdf', [App\Http\Controllers\PdfController::class, 'generate'])->name('sales.pdf');
});
