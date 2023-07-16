<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SipMobileController;

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
Route::apiResource('/jmos', App\Http\Controllers\Api\JmoController::class);
//Route::apiResource('/sipmobile', App\Http\Controllers\Api\SipMobileController::class);

Route::post('/sipmobile/login', [SipMobileController::class, 'login']);
Route::post('/sipmobile/refreshcalldata', [SipMobileController::class, 'refreshcalldata']);
Route::post('/sipmobile/refreshcalldata_back', [SipMobileController::class, 'refreshcalldata_back']);
Route::post('/sipmobile/calldetail', [SipMobileController::class, 'calldata_detail']);
Route::post('/sipmobile/savecalldata', [SipMobileController::class, 'savecalldata']);
Route::post('/sipmobile/statuscall', [SipMobileController::class, 'statuscall']);
