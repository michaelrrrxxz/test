<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\QuotationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('quotations', QuotationController::class);
    Route::get('customers/{customer}/quotations', [QuotationController::class, 'byCustomer']);
    Route::post('customers/{customer}/quotations', [QuotationController::class, 'store']);
    Route::post('quotations/{quotation}/send-email', [QuotationController::class, 'sendEmail'
]);

});
