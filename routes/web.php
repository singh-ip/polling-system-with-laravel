<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->middleware('throttle:10,1')->name('polls.vote');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
});
