<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\StudentClassroomController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\TeacherdashboardController;
use App\Http\Controllers\TeacherQuizController;
use App\Http\Controllers\TeacherClassroomController;
use App\Http\Controllers\TeacherAiQuizController;
use App\Http\Controllers\TeacherprofileController;

Route::get('/', [WelcomeController::class, 'show'])->name('welcome');
Route::get('/home', [HomeController::class, 'show'])->name('home');//show the dashboard page

Route::get('/signup', [SignupController::class, 'create']); //show the form of sign up
Route::post('/signup', [SignupController::class, 'store']);//Process the form submission of signup
Route::get('/signin', [SigninController::class, 'create']);//show the form of sign in
Route::post('/signin', [SigninController::class, 'store']); //Process the form submission of signin

Route::middleware('auth')->group(function () {
    Route::get('/mydecks', [DeckController::class, 'index'])->name('mydecks');
    Route::post('/decks', [DeckController::class, 'store'])->name('decks.store');
    Route::get('/decks/{deck}', [DeckController::class, 'show'])->name('decks.show');
    Route::put('/decks/{deck}', [DeckController::class, 'update'])->name('decks.update');
    Route::delete('/decks/{deck}', [DeckController::class, 'destroy'])->name('decks.destroy');

    Route::post('/decks/{deck}/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::delete('/flashcards/{flashcard}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::put('/flashcards/{flashcard}', [FlashcardController::class, 'update'])->name('flashcards.update');

    // Study mode routes
    Route::get('/decks/{deck}/study/flipcards', [StudyController::class, 'flipcards'])->name('study.flipcards');
    Route::get('/decks/{deck}/study/multiplechoice', [StudyController::class, 'multiplechoice'])->name('study.multiplechoice');
    Route::get('/decks/{deck}/study/identification', [StudyController::class, 'identification'])->name('study.identification');

    // Profile routes
    Route::get('/myprofile', [MyProfileController::class, 'show'])->name('myprofile');
    Route::put('/myprofile', [MyProfileController::class, 'update'])->name('myprofile.update');
    Route::delete('/myprofile', [MyProfileController::class, 'destroy'])->name('myprofile.destroy');

    // Student Assignments & Classrooms
    Route::get('/assignments', [StudentClassroomController::class, 'index'])->name('student.assignments');
    Route::get('/classroom/{classroom}', [StudentClassroomController::class, 'show'])->name('student.classroom.show');

    // Student Quiz Taking
    Route::get('/quiz/{assignment}/take', [StudentQuizController::class, 'take'])->name('student.quiz.take');
    Route::post('/quiz/{assignment}/submit', [StudentQuizController::class, 'submit'])->name('student.quiz.submit');
    Route::get('/quiz/results/{attempt}', [StudentQuizController::class, 'results'])->name('student.quiz.results');

    // Student join classroom via code
    Route::post('/classroom/join', [StudentClassroomController::class, 'join'])->name('classroom.join');
});


// ─── Teacher Routes (auth + teacher middleware) ───

Route::middleware('auth')->group(function (){
    Route::get('/teacher-dashboard', [TeacherdashboardController::class, 'show'])->name('teacher.dashboard');

});

Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {

    // Classrooms
    Route::post('/classrooms', [TeacherClassroomController::class, 'store'])->name('classroom.store');
    Route::get('/classrooms/{classroom}', [TeacherClassroomController::class, 'show'])->name('classroom.show');
    Route::delete('/classrooms/{classroom}', [TeacherClassroomController::class, 'destroy'])->name('classroom.destroy');
    Route::post('/classrooms/{classroom}/students', [TeacherClassroomController::class, 'addStudent'])->name('classroom.addStudent');
    Route::delete('/classrooms/{classroom}/students/{user}', [TeacherClassroomController::class, 'removeStudent'])->name('classroom.removeStudent');

    // Quizzes
    Route::get('/quizzes', [TeacherQuizController::class, 'index'])->name('quiz.index');
    Route::get('/quizzes/create', [TeacherQuizController::class, 'create'])->name('quiz.create');
    Route::post('/quizzes', [TeacherQuizController::class, 'store'])->name('quiz.store');
    Route::get('/quizzes/{quiz}', [TeacherQuizController::class, 'show'])->name('quiz.show');
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quiz.destroy');
    Route::post('/quizzes/assign', [TeacherQuizController::class, 'assign'])->name('quiz.assign');
    Route::delete('/quizzes/assignments/{assignment}', [TeacherQuizController::class, 'unassign'])->name('quiz.unassign');

    // AI Quiz (front-end only)
    Route::get('/quizzes/ai', [TeacherAiQuizController::class, 'showUpload'])->name('quiz.ai');
    Route::post('/quizzes/ai', [TeacherAiQuizController::class, 'generate'])->name('quiz.ai.generate');

    // Profile
    Route::get('/profile', [TeacherprofileController::class, 'show'])->name('profile');
    Route::put('/profile', [TeacherprofileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [TeacherprofileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
