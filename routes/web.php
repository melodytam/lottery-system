<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\UserController;

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

Route::get('/token', function () {
    return csrf_token(); 
});

Route::prefix('user')->group(function () {
    Route::post('/', [UserController::class, 'create'])->name('create_user');
    Route::get('/', [UserController::class, 'list'])->name('list_user');
    Route::get('/{id}', [UserController::class, 'read'])->name('read_user');
    Route::get('/username/{id}', [UserController::class, 'readByUsername'])->name('read_user_by_username');
    Route::put('/{id}', [UserController::class, 'update'])->name('update_user');
});

Route::prefix('ticket')->group(function () {
    Route::post('/', [TicketController::class, 'create'])->name('create_ticket');
    Route::get('/', [TicketController::class, 'list'])->name('list_ticket');
    Route::get('/{id}', [TicketController::class, 'read'])->name('read_ticket');
    Route::get('/ticketNo/{ticketNo}', [TicketController::class, 'readByTicketNo'])->name('read_ticket_by_ticket_no');
});

Route::prefix('draw')->group(function () {
    Route::get('/drawTicket', [DrawController::class, 'create'])->name('create_draw');
    Route::get('/{id}', [DrawController::class, 'read'])->name('read_draw');
    Route::get('/', [DrawController::class, 'list'])->name('list_draw');
});
