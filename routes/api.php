<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\RecurrenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('throttle:20,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::post('/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/reset', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/tenants/me', [TenantController::class, 'me']);

    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/invites', [MemberController::class, 'invite']);

    Route::get('/accounts', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{id}', [AccountController::class, 'show']);
    Route::patch('/accounts/{id}', [AccountController::class, 'update']);
    Route::post('/accounts/{id}/members', [AccountController::class, 'addMember']);

    Route::post('/cards', [CardController::class, 'store']);
    Route::get('/cards', [CardController::class, 'index']);
    Route::get('/cards/{id}', [CardController::class, 'show']);
    Route::post('/cards/{id}/purchase', [CardController::class, 'purchase']);
    Route::post('/cards/{id}/close', [CardController::class, 'close']);
    Route::get('/cards/{id}/invoices', [InvoiceController::class, 'byCard']);
    Route::post('/invoices/{id}/pay', [InvoiceController::class, 'pay']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('recurrences', RecurrenceController::class);
    Route::apiResource('goals', GoalController::class);
    Route::get('/goals/{id}/progress', [GoalController::class, 'progress']);

    Route::get('/reports/cashflow', [ReportController::class, 'cashflow']);
    Route::get('/reports/by-category', [ReportController::class, 'byCategory']);
    Route::get('/reports/balance-summary', [ReportController::class, 'balanceSummary']);
    Route::get('/reports/goals', [ReportController::class, 'goals']);

    Route::post('/import/csv', [ImportController::class, 'importCsv']);
});
