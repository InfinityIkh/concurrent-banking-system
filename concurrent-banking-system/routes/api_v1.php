<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class ,'login']);
    Route::post('/register', [AuthController::class ,'register']);
});
//
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
    Route::middleware('account.active')->group(function () {
        Route::middleware('admin')->group(function () {
            Route::apiResource('users' , UserController::class);
            Route::apiResource('currencies' ,CurrencyController::class);
            Route::get('accounts' , [AccountController::class ,'index']);
            Route::post('accounts/{user}', [AccountController::class, 'store']);
            Route::put('accounts/{account}', [AccountController::class, 'update']);
            Route::get('accounts/{account}', [AccountController::class, 'show']);
            Route::delete('accounts/{account}', [AccountController::class, 'destroy']);
            Route::patch('accounts/{id}/activate', [AccountController::class, 'activateAccount']);
            Route::patch('accounts/{id}/deactivate', [AccountController::class, 'deactivateAccount']);
            Route::patch('accounts/{id}/close', [AccountController::class, 'closeAccount']);
            Route::apiResource('transactions' , TransactionController::class);
        });
        Route::post('/transactions/transfer/{toAccount}', [TransactionController::class, 'createTransferOperation']);
        Route::post('/transactions/deposit', [TransactionController::class, 'createDepositOperation']);
        Route::post('/transactions/withdraw', [TransactionController::class, 'createWithdrawOperation']);
        Route::post('/transactions/refund/{transaction}', [TransactionController::class, 'createRefundOperation']);
    });
});
