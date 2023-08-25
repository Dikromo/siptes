<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JmoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CallpagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatuscallController;

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
Route::get('/customer/cekdbr', [CustomerController::class, 'viewCekdbr'])->middleware('auth');
Route::post('/customer/ajax/cekdbr', [CustomerController::class, 'cekDbr'])->middleware('auth');
Route::get('/customer/callhistory', [CustomerController::class, 'viewCallhistory'])->middleware('auth');
Route::post('/customer/ajax/callhistory', [CustomerController::class, 'callhistory'])->middleware('auth');
Route::post('/customer/callhistory/detail', [CustomerController::class, 'callhistoryEdit'])->middleware('auth');
Route::put('/customer/callhistory/{callhistoryid}', [CustomerController::class, 'callhistoryStoremodal'])->middleware('auth');
Route::get('/customer/logdistribusi', [CustomerController::class, 'viewlogDistribusi'])->middleware('auth');
Route::post('/customer/ajax/logdistribusi', [CustomerController::class, 'logDistribusi'])->middleware('auth');

Route::get('/campaign', [CampaignController::class, 'index'])->middleware('auth');
Route::post('/campaign/ajax', [CampaignController::class, 'dataTables'])->middleware('auth');

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


Route::get('/mutasi', [MutasiController::class, 'index'])->middleware('auth');
Route::post('/mutasi/ajax', [MutasiController::class, 'dataTables'])->middleware('auth');
Route::get('/mutasi/create', [MutasiController::class, 'mutasiFormadd'])->middleware('auth');
Route::post('/mutasi', [MutasiController::class, 'mutasiStore'])->middleware('auth');
Route::put('/mutasi/{mutasiid}', [MutasiController::class, 'mutasiStore'])->middleware('auth');
Route::get('/mutasi/{id}/edit', [MutasiController::class, 'mutasiEdit'])->middleware('auth');
Route::post('/mutasilist', [MutasiController::class, 'mutasilistStore'])->middleware('auth');
Route::post('/mutasilist/detail', [MutasiController::class, 'mutasilistEdit'])->middleware('auth');
Route::post('/mutasilist/ajax', [MutasiController::class, 'mutasilistdataTables'])->middleware('auth');
Route::put('/mutasilist/{mutasilistid}', [MutasiController::class, 'mutasilistStore'])->middleware('auth');


Route::get('/statuscall', [StatuscallController::class, 'index'])->middleware('auth');
Route::post('/statuscall/ajax', [StatuscallController::class, 'dataTables'])->middleware('auth');
Route::get('/statuscall/create', [StatuscallController::class, 'statuscallFormadd'])->middleware('auth');
Route::post('/statuscall', [StatuscallController::class, 'statuscallStore'])->middleware('auth');
Route::put('/statuscall/{statuscallid}', [StatuscallController::class, 'statuscallStore'])->middleware('auth');
Route::get('/statuscall/{id}/edit', [StatuscallController::class, 'statuscallEdit'])->middleware('auth');


Route::post('/cek/sm', [SelectController::class, 'getSM'])->middleware('auth');
Route::post('/cek/um', [SelectController::class, 'getUM'])->middleware('auth');
Route::post('/cek/produkspv', [SelectController::class, 'getProdukspv'])->middleware('auth');


Route::get('/dashboard/sales', [DashboardController::class, 'salescall'])->middleware('auth');
Route::get('/dashboard/sales2', [DashboardController::class, 'salescall2'])->middleware('auth');
Route::post('/dashboard/ajaxsalescall', [DashboardController::class, 'getSalescall'])->middleware('auth');
Route::post('/dashboard/ajaxsalescall2', [DashboardController::class, 'getSalescall2'])->middleware('auth');
Route::post('/dashboard/ajaxsalescall2/detail', [DashboardController::class, 'getSalescall2_detail'])->middleware('auth');
Route::post('/dashboard/ajaxcampaigncall', [DashboardController::class, 'getCampaigncall'])->middleware('auth');
Route::post('/dashboard/ajaxcampaigncall/detail', [DashboardController::class, 'getCampaigncall_detail'])->middleware('auth');

Route::get('/admin', function () {
    if (auth()->user()->roleuser_id == '3') {
        return redirect('/call');
    } else {
        return view('admin.tes', ['title' => 'Administrator', 'active' => 'dashboard', 'active_sub' => '']);
    }
})->middleware('auth');
Route::get('/tes1', function () {
    return view('admin.tes', ['title' => 'tes1', 'active' => 'menu1', 'active_sub' => 'menu_sub1']);
})->middleware('auth');
