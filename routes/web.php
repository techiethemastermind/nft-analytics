<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\TokensController::class, 'index'])->name('home');
Route::get('/tokens', [App\Http\Controllers\ScrapController::class, 'scrappingTokens'])->name('tokens');
Route::get('/transactions', [App\Http\Controllers\ScrapController::class, 'scrappingTransactions'])->name('transactions');
Route::get('/transactions-update', [App\Http\Controllers\ScrapController::class, 'updatingTransactions'])->name('transactions.update');
Route::get('/transactions/items', [App\Http\Controllers\ScrapController::class, 'scrappingTransactionItems'])->name('transactions.items');
Route::get('/transactions/items-update', [App\Http\Controllers\ScrapController::class, 'updatingTransactionItems'])->name('transactions.items.update');
Route::get('/stop', [App\Http\Controllers\ScrapController::class, 'stopScrapping'])->name('stop');
Route::get('/action', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Pages
Route::get('/nft-total-overview', [App\Http\Controllers\NftOverviewController::class, 'index'])->name('nft-overview');
Route::get('/floor-daily-overview', [App\Http\Controllers\FloorOverviewController::class, 'index'])->name('floor-overview');
Route::get('/nft-sniper', [App\Http\Controllers\NftSniperController::class, 'index'])->name('nft-sniper');
Route::get('/dashboard', [App\Http\Controllers\TokensController::class, 'index'])->name('page.token');
Route::post('/list/token', [App\Http\Controllers\TokensController::class, 'getList'])->name('list.token');
Route::post('/list/token/update', [App\Http\Controllers\TokensController::class, 'updateTokenData'])->name('list.token.update');

// Manual Action
Route::post('/tokens/update', [App\Http\Controllers\TokensController::class, 'updateTokens'])->name('update.tokens');
Route::post('/transactions/update', [App\Http\Controllers\TokensController::class, 'updateTransactions'])->name('update.transactions');
Route::post('/transactions/update/details', [App\Http\Controllers\TokensController::class, 'updateTransactionDetails'])->name('update.details');
