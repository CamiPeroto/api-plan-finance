<?php

use App\Http\Controllers\Api\AvailableMoneyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (login e registro)
Route::post('register', [RegisterController::class, 'store']);
Route::post('login', [LoginController::class, 'store']);

// Rotas protegidas com Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('logout', [LoginController::class, 'destroy']);

    // Entradas (AvailableMoney)
    Route::get('entrada', [AvailableMoneyController::class, 'index']);
    Route::post('entrada/search', [AvailableMoneyController::class, 'search']);
    Route::post('entrada', [AvailableMoneyController::class, 'store']);
    Route::get('entrada/{id}', [AvailableMoneyController::class, 'show']);
    Route::put('entrada/{id}', [AvailableMoneyController::class, 'update']);
    Route::delete('entrada/{id}', [AvailableMoneyController::class, 'destroy']);

    // Despesas (Finance)
    Route::get('despesa', [FinanceController::class, 'index']);
    Route::post('despesa/search', [FinanceController::class, 'search']);
    Route::post('despesa', [FinanceController::class, 'store']);
    Route::get('despesa/{id}', [FinanceController::class, 'show']);
    Route::put('despesa/{id}', [FinanceController::class, 'update']);
    Route::delete('despesa/{id}', [FinanceController::class, 'destroy']);

    // Categorias
    Route::get('categoria', [CategoryController::class, 'index']);
    Route::post('categoria/search', [CategoryController::class, 'search']);
    Route::post('categoria', [CategoryController::class, 'store']);
    Route::get('categoria/{id}', [CategoryController::class, 'show']);
    Route::put('categoria/{id}', [CategoryController::class, 'update']);
    Route::delete('categoria/{id}', [CategoryController::class, 'destroy']);
});
