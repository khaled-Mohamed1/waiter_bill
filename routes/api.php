<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\CashManagementController;
use App\Http\Controllers\Api\OverviewController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\TicketController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/clear-cache-all', function() {
    \Artisan::call('cache:clear');

    \Artisan::call('view:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:cache');
    return 'done';
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/forget-password', [PasswordReset::class, 'forgetPassword']);
    Route::post('/reset-password', [PasswordReset::class, 'resetPassword']);

});

Route::group([

    'middleware' => ['auth:sanctum'],

], function ($router) {

    Route::prefix('profile')->group(function () {

        Route::get('/', [ProfileController::class, 'index']);
        Route::get('/receipt', [ProfileController::class, 'showReceipts']);
        Route::post('/update_password', [ProfileController::class, 'updatePassword']);
        Route::post('/logout', [ProfileController::class, 'logout']);
        Route::post('/confirm_pin', [ProfileController::class, 'confirmPin']);

    });

    Route::prefix('categories')->group(function () {

        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::post('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'delete']);

    });

    Route::prefix('products')->group(function () {

        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'delete']);

    });

    Route::prefix('customers')->group(function () {

        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::post('/{id}', [CustomerController::class, 'update']);
        Route::post('/{id}/update_status', [CustomerController::class, 'updateStatus']);
        Route::delete('/{id}', [CustomerController::class, 'delete']);

    });

    Route::prefix('receipts')->group(function () {

        Route::get('/', [ReceiptController::class, 'index']);
        Route::get('/{id}', [ReceiptController::class, 'show']);
        Route::post('/', [ReceiptController::class, 'store']);
        Route::post('/refund/{id}', [ReceiptController::class, 'refund']);

    });

    Route::prefix('employees')->group(function () {

        Route::get('/', [EmployeeController::class, 'index']);

    });

    Route::prefix('suggestions')->group(function () {

        Route::post('/', [SuggestionController::class, 'store']);

    });

    Route::prefix('shifts')->group(function () {

        Route::get('/', [ShiftController::class, 'index']);
        Route::post('/', [ShiftController::class, 'store']);
        Route::get('/{id}', [ShiftController::class, 'show']);
        Route::post('/{id}', [ShiftController::class, 'update']);
        Route::post('/{id}/cash', [CashManagementController::class, 'store']);

    });

    Route::prefix('tables')->group(function () {

        Route::get('/', [TableController::class, 'index']);
        Route::post('/', [TableController::class, 'store']);
        Route::get('/{id}', [TableController::class, 'show']);
        Route::post('/{id}', [TableController::class, 'update']);
        Route::delete('/{id}', [TableController::class, 'delete']);

    });

    Route::prefix('tickets')->group(function () {

        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::get('/{id}/paid', [TicketController::class, 'paid']);
        Route::post('/{id}', [TicketController::class, 'update']);
        Route::delete('/{id}', [TicketController::class, 'delete']);

    });

    Route::prefix('overviews')->group(function () {

        Route::get('/statistics', [OverviewController::class, 'statistics']);

    });


});

