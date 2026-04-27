<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'show'])->name('welcome');

Route::get('/signup', [SignupController::class, 'create']); //show the form of sign up

Route::post('/signup', [SignupController::class, 'store']);//Process the form submission of signup

Route::get('/signin', [SigninController::class, 'create']);//show the form of sign in

Route::post('/signin', [SigninController::class, 'store']); //Process the form submission of signin

Route::get('/home', [HomeController::class, 'show'])->name('home');//show the dashboard page

Route::middleware('auth')->group(function () {
    Route::get('/mydecks', [DeckController::class, 'index'])->name('mydecks');
    Route::post('/decks', [DeckController::class, 'store'])->name('decks.store');
    Route::get('/decks/{deck}', [DeckController::class, 'show'])->name('decks.show');
    Route::put('/decks/{deck}', [DeckController::class, 'update'])->name('decks.update');
    Route::delete('/decks/{deck}', [DeckController::class, 'destroy'])->name('decks.destroy');
    
    Route::post('/decks/{deck}/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::delete('/flashcards/{flashcard}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
});

Route::get('/myprofile', [MyProfileController::class, 'show'])->name('myprofile');

// Teacher Static Routes
Route::view('/teacher/dashboard', 'teacher.teacher-dashboard')->name('teacher-dashboard');
Route::view('/teacher/assign-quiz', 'teacher.assign-quiz')->name('assign-quiz');
Route::view('/teacher/profile', 'teacher.teacher-profile')->name('teacher-profile');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
