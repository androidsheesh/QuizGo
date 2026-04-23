<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;

Route::get('/', function () {
    return view('welcome');
});
//show the form of sign up
Route::get('/signup', [SignupController::class, 'create']);
//Process the form submission
Route::post('/signup', [SignupController::class, 'store']);

//show the form of sign in
Route::get('/signin', [SigninController::class, 'create']);

//process the form submission
Route::post('/signin', [SigninController::class, 'store']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
