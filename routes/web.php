<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\GenerationStatusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\StudentClassroomController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\TeacherAiQuizController;
use App\Http\Controllers\TeacherClassroomController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TeacherQuizController;
use App\Http\Controllers\TeacherSigninController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

// ─── Public Routes (no auth) ───
Route::get('/', [WelcomeController::class, 'show'])->name('welcome');

Route::get('/favicon.svg', function () {
    // Render the raw SVG component, NOT the wrapper!
    $svg = Blade::render('<x-logo-svg />');

    return response($svg)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Cache-Control', 'public, max-age=31536000');
});

Route::get('/signup', [SignupController::class, 'create']);
Route::post('/signup', [SignupController::class, 'store'])->middleware('throttle:3,1');

Route::get('/signin', [SigninController::class, 'create'])->name('signin');
Route::post('/signin', [SigninController::class, 'store'])->middleware('throttle:login');

Route::get('/teacher/signin', [TeacherSigninController::class, 'create'])->name('teacher.signin');
Route::post('/teacher/signin', [TeacherSigninController::class, 'store'])->middleware('throttle:login')->name('teacher.signin.store');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// ─── Any signed-in user ───
Route::middleware(['auth', 'session.token'])->group(function () {
    Route::get('/api/generations/{uuid}', GenerationStatusController::class)->name('generations.status');
});

// ─── Student Routes ───
Route::middleware(['auth', 'role:student', 'prevent-back', 'session.token'])->group(function () {

    Route::get('/home', [HomeController::class, 'show'])->name('home');

    Route::post('/generate/topic', [HomeController::class, 'generateFromTopic'])->name('generate.topic');
    Route::post('/generate/text', [HomeController::class, 'generateFromText'])->name('generate.text');
    Route::post('/generate/pdf', [HomeController::class, 'generateFromPdf'])->name('generate.pdf');

    // Decks
    Route::get('/mydecks', [DeckController::class, 'index'])->name('mydecks');
    Route::post('/decks', [DeckController::class, 'store'])->name('decks.store');
    Route::get('/decks/{deck}', [DeckController::class, 'show'])->name('decks.show');
    Route::put('/decks/{deck}', [DeckController::class, 'update'])->name('decks.update');
    Route::delete('/decks/{deck}', [DeckController::class, 'destroy'])->name('decks.destroy');

    // Flashcards
    Route::post('/decks/{deck}/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::delete('/flashcards/{flashcard}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::put('/flashcards/{flashcard}', [FlashcardController::class, 'update'])->name('flashcards.update');

    // Study mode
    Route::get('/decks/{deck}/study/flipcards', [StudyController::class, 'flipcards'])->name('study.flipcards');
    Route::get('/decks/{deck}/study/multiplechoice', [StudyController::class, 'multiplechoice'])->name('study.multiplechoice');
    Route::get('/decks/{deck}/study/identification', [StudyController::class, 'identification'])->name('study.identification');

    // Profile
    Route::get('/myprofile', [ProfileController::class, 'show'])->name('myprofile');
    Route::put('/myprofile', [ProfileController::class, 'update'])->name('myprofile.update');
    Route::put('/myprofile/password', [ProfileController::class, 'updatePassword'])->name('myprofile.password');
    Route::delete('/myprofile', [ProfileController::class, 'destroy'])->name('myprofile.destroy');

    // Classrooms
    Route::get('/assignments', [StudentClassroomController::class, 'index'])->name('student.assignments');
    Route::get('/classroom/{classroom}', [StudentClassroomController::class, 'show'])->name('student.classroom.show');
    Route::post('/classroom/join', [StudentClassroomController::class, 'join'])->name('classroom.join');

    // Quizzes
    Route::get('/quiz/{assignment}/take', [StudentQuizController::class, 'take'])->name('student.quiz.take');
    Route::post('/quiz/{assignment}/submit', [StudentQuizController::class, 'submit'])->name('student.quiz.submit');
    Route::get('/quiz/results/{attempt}', [StudentQuizController::class, 'results'])->name('student.quiz.results');
});

// ─── Admin Routes ───
Route::middleware(['auth', 'role:admin', 'prevent-back', 'session.token'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    Route::delete('/teachers/{teacher}', [AdminController::class, 'deleteTeacher'])->name('teachers.destroy');
});

// ─── Teacher Routes ───
Route::middleware(['auth', 'role:teacher', 'prevent-back', 'session.token'])->prefix('teacher')->name('teacher.')->group(function () {

    Route::get('/dashboard', [TeacherDashboardController::class, 'show'])->name('dashboard');

    // Classrooms
    Route::post('/classrooms', [TeacherClassroomController::class, 'store'])->name('classroom.store');
    Route::get('/classrooms/{classroom}', [TeacherClassroomController::class, 'show'])->name('classroom.show');
    Route::delete('/classrooms/{classroom}', [TeacherClassroomController::class, 'destroy'])->name('classroom.destroy');
    Route::post('/classrooms/{classroom}/students', [TeacherClassroomController::class, 'addStudent'])->name('classroom.addStudent');
    Route::delete('/classrooms/{classroom}/students/{user}', [TeacherClassroomController::class, 'removeStudent'])->name('classroom.removeStudent');

    // Quizzes
    Route::get('/quizzes', [TeacherQuizController::class, 'index'])->name('quiz.index');
    Route::post('/quizzes', [TeacherQuizController::class, 'store'])->name('quiz.store');
    Route::post('/quizzes/assign', [TeacherQuizController::class, 'assign'])->name('quiz.assign');
    Route::delete('/quizzes/assignments/{assignment}', [TeacherQuizController::class, 'unassign'])->name('quiz.unassign');
    Route::post('/quizzes/ai', [TeacherAiQuizController::class, 'generate'])->name('quiz.ai.generate');
    Route::get('/quizzes/{quiz}', [TeacherQuizController::class, 'show'])->name('quiz.show');
    Route::patch('/quizzes/{quiz}/active', [TeacherQuizController::class, 'toggleActive'])->name('quiz.toggle');
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quiz.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
