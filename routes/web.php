<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MydecksController;
use App\Http\Controllers\MyProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/signup', [SignupController::class, 'create']); //show the form of sign up

Route::post('/signup', [SignupController::class, 'store']);//Process the form submission of signup

Route::get('/signin', [SigninController::class, 'create']);//show the form of sign in

Route::post('/signin', [SigninController::class, 'store']); //Process the form submission of signin

Route::get('/home', [HomeController::class, 'show'])->name('home');//show the dashboard page

Route::get('/mydecks', [MydecksController::class, 'show'])->name('mydecks'); // show the my decks

Route::get('/myprofile', [MyProfileController::class, 'show'])->name('myprofile');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
