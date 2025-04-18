<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User;
use App\Livewire\Product;
use App\Livewire\Report;
use App\Livewire\Transaction;
use App\Livewire\Home;
use App\Http\Controllers\ReceiptController;

Route::get('/', function () {
    $user = Auth::user();

    if ($user) {
        if ($user->role === 'admin') {
            return redirect('/home');
        } elseif ($user->role === 'kasir') {
            return redirect('/transaction');
        }
    }

    return view('auth.login');
});

Auth::routes();

Route::get('/home', Home::class)->name('home');
Route::get('/user', User::class)->middleware(['auth'])->name('user');
Route::get('/product', Product::class)->middleware(['auth'])->name('product');
Route::get('/report', Report::class)->name('report');
Route::get('/transaction', Transaction::class)->middleware(['auth'])->name('transaction');
Route::get('/receipt/{transaction_id}', [ReceiptController::class, 'show'])->name('receipt');