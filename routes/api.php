<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// Routes untuk Customer (Master)
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

// Routes untuk Transaksi (TTH)
Route::get('/tth', [TransactionController::class, 'index']); // List hadiah + nama toko
Route::get('/tth/{ttottpNo}', [TransactionController::class, 'show']); // Detail per transaksi
Route::post('/tth', [TransactionController::class, 'store']); // Insert TTH + Detail
Route::delete('/tth/{ttottpNo}', [TransactionController::class, 'destroy']); // Delete

// Routes khusus Update Detail (Sesuai request: Detail bisa edit qty/jenis)
Route::put('/tth-detail/{id}', [TransactionController::class, 'updateDetail']);