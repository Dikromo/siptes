<?php

use App\Http\Controllers\CallpagesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JmoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatuscallController;
use App\Http\Controllers\UserController;
use App\Models\Statuscall;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->middleware('guest');

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'auth'])->middleware('guest');


Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');


Route::get('/user', [UserController::class, 'index'])->middleware('auth');
Route::post('/user/ajax', [UserController::class, 'dataTables'])->middleware('auth');
Route::get('/user/create', [UserController::class, 'userFormadd'])->middleware('auth');
Route::post('/user', [UserController::class, 'userStore'])->middleware('auth');
Route::put('/user/{user:username}', [UserController::class, 'userStore'])->middleware('auth');
Route::get('/user/{user:username}', [UserController::class, 'userShow'])->middleware('auth');
Route::get('/user/{user:username}/edit', [UserController::class, 'userEdit'])->middleware('auth');
Route::delete('/user', [UserController::class, 'userDestroy'])->middleware('auth');

Route::get('/customer/import', [CustomerController::class, 'customerFormimport'])->middleware('auth');
Route::post('/customer/import', [CustomerController::class, 'customerImport'])->middleware('auth');
Route::get('/customer/distribusi', [CustomerController::class, 'customerDistribusi'])->middleware('auth');
Route::post('/customer/distribusi/proses', [CustomerController::class, 'customerDistribusiproses'])->middleware('auth');
Route::post('/customer/ajax/from', [CustomerController::class, 'customerDistribusifrom'])->middleware('auth');
Route::post('/customer/ajax/to', [CustomerController::class, 'customerDistribusito'])->middleware('auth');

Route::get('/call', [CallpagesController::class, 'salesCallpages'])->middleware('auth');
Route::get('/call/detail/{id}', [CallpagesController::class, 'salesCallshow'])->middleware('auth');
Route::put('/call/detail/{id}', [CallpagesController::class, 'salescallStore'])->middleware('auth');
Route::post('/call/ajax', [CallpagesController::class, 'salesCallback'])->middleware('auth');


Route::get('/jmosip', [JmoController::class, 'index'])->middleware('auth');
Route::post('/jmosip/ajax', [JmoController::class, 'dataTables'])->middleware('auth');
Route::get('/jmosip/create', [JmoController::class, 'jmoFormadd'])->middleware('auth');
Route::post('/jmosip', [JmoController::class, 'jmoStore'])->middleware('auth');
Route::put('/jmosip/{jmoid}', [JmoController::class, 'jmoStore'])->middleware('auth');
Route::get('/jmosip/{id}/edit', [JmoController::class, 'jmoEdit'])->middleware('auth');


Route::get('/statuscall', [StatuscallController::class, 'index'])->middleware('auth');
Route::post('/statuscall/ajax', [StatuscallController::class, 'dataTables'])->middleware('auth');
Route::get('/statuscall/create', [StatuscallController::class, 'statuscallFormadd'])->middleware('auth');
Route::post('/statuscall', [StatuscallController::class, 'statuscallStore'])->middleware('auth');
Route::put('/statuscall/{statuscallid}', [StatuscallController::class, 'statuscallStore'])->middleware('auth');
Route::get('/statuscall/{id}/edit', [StatuscallController::class, 'statuscallEdit'])->middleware('auth');


Route::get('/admin', function () {
    return view('admin.tes', ['title' => 'Administrator', 'active' => 'dashboard', 'active_sub' => '']);
})->middleware('auth');
Route::get('/tes1', function () {
    return view('admin.tes', ['title' => 'tes1', 'active' => 'menu1', 'active_sub' => 'menu_sub1']);
})->middleware('auth');
