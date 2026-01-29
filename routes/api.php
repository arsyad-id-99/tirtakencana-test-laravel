<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController; 
use App\Http\Controllers\Api\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes untuk Customer (Master)
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

// Routes untuk Transaksi (TTH)
Route::get('/tth', [TransactionController::class, 'index']);
Route::get('/tth/{ttottpNo}', [TransactionController::class, 'show']); 
Route::post('/tth', [TransactionController::class, 'store']); 
Route::delete('/tth/{ttottpNo}', [TransactionController::class, 'destroy']); 

Route::put('/tth-detail/{id}', [TransactionController::class, 'updateDetail']);