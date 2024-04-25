<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\ReportSalesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SalesSummaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/roles/{id}', [RolesController::class, 'show']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::put('/roles', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);


    Route::get('/categories', [ProductCategoryController::class, 'index']);
    Route::get('/categories/{id}', [ProductCategoryController::class, 'show']);
    Route::post('/categories', [ProductCategoryController::class, 'store']);
    Route::put('/categories', [ProductCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [ProductCategoryController::class, 'destroy']);


    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/promo', [PromoController::class, 'index']);
    Route::get('/promo/{id}', [PromoController::class, 'show']);
    Route::post('/promo', [PromoController::class, 'store']);
    Route::put('/promo', [PromoController::class, 'update']);
    Route::delete('/promo/{id}', [PromoController::class, 'destroy']);

    Route::get('/vouchers', [VoucherController::class, 'index']);
    Route::get('/vouchers/{id}', [VoucherController::class, 'show']);
    Route::post('/vouchers', [VoucherController::class, 'store']);
    Route::put('/vouchers', [VoucherController::class, 'update']);
    Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy']);

    Route::get('/discount', [DiscountController::class, 'index']);
    Route::get('/discount/{id}', [DiscountController::class, 'show']);
    Route::post('/discount', [DiscountController::class, 'store']);
    Route::put('/discount', [DiscountController::class, 'update']);
    Route::delete('/discount/{id}', [DiscountController::class, 'destroy']);

    Route::get('/sale', [SalesController::class, 'index']);
    Route::get('/sale/{id}', [SalesController::class, 'show']);
    Route::post('/sale', [SalesController::class, 'store']);
    Route::put('/sale', [SalesController::class, 'update']);
    Route::delete('/sale/{id}', [SalesController::class, 'destroy']);

    Route::get('/report/sales-promo', [ReportSalesController::class, 'viewSalesPromo']);
    Route::get('/report/sales-transaction', [ReportSalesController::class, 'viewSalesTransaction']);


    Route::get('/report/sales-menu', [ReportSalesController::class, 'viewSalesCategories']);
    Route::get('/download/sales-category', [ReportSalesController::class, 'viewSalesCategories']);

    Route::get('/report/sales-customer', [ReportSalesController::class, 'viewSalesCustomers']);
    Route::get('/download/sales-customer', [ReportSalesController::class, 'viewSalesCustomers']);
    Route::get('/report/sales-customer/{id}/{date}', [ReportSalesController::class, 'viewSalesCustomersPerDate']);

    Route::get('/report/total-sales/summaries', [SalesSummaryController::class, 'getTotalSummary']);
    Route::get('/report/total-sales/year', [SalesSummaryController::class, 'getDiagramPerYear']);
    Route::get('/report/total-sales/month/{year}', [SalesSummaryController::class, 'getDiagramPerMonth']);
    Route::get('/report/total-sales/day/', [SalesSummaryController::class, 'getDiagramPerCustomDate']);

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware(['auth.api']);
});

Route::get('/', function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});

/**
 * Jika Frontend meminta request endpoint API yang tidak terdaftar
 * maka akan menampilkan HTTP 404
 */
Route::fallback(function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});
