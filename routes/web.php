<?php

use App\Http\Controllers\DeckController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\StudentClassroomController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\TeacherAiQuizController;
use App\Http\Controllers\TeacherClassroomController;
use App\Http\Controllers\TeacherdashboardController;
use App\Http\Controllers\TeacherprofileController;
use App\Http\Controllers\TeacherQuizController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TeacherSigninController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes (no auth) ───
Route::get('/', [WelcomeController::class, 'show'])->name('welcome');

Route::get('/signup', [SignupController::class, 'create']);
Route::post('/signup', [SignupController::class, 'store'])->middleware('throttle:3,1');

Route::get('/signin', [SigninController::class, 'create']);
Route::post('/signin', [SigninController::class, 'store']);

Route::get('/teacher/signin', [TeacherSigninController::class, 'create'])->name('teacher.signin');
Route::post('/teacher/signin', [TeacherSigninController::class, 'store'])->name('teacher.signin.store');

// ─── Student Routes ───
Route::middleware(['auth', 'prevent-back'])->group(function () {

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
    Route::get('/myprofile', [MyProfileController::class, 'show'])->name('myprofile');
    Route::put('/myprofile', [MyProfileController::class, 'update'])->name('myprofile.update');
    Route::put('/myprofile/password', [MyProfileController::class, 'updatePassword'])->name('myprofile.password');
    Route::delete('/myprofile', [MyProfileController::class, 'destroy'])->name('myprofile.destroy');

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
Route::middleware(['auth', 'prevent-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    Route::delete('/teachers/{teacher}', [AdminController::class, 'deleteTeacher'])->name('teachers.destroy');
});

// ─── Teacher Routes ───
Route::middleware(['auth', 'prevent-back'])->group(function () {
    Route::get('/teacher-dashboard', [TeacherdashboardController::class, 'show'])->name('teacher.dashboard');
});

Route::middleware(['auth', 'teacher', 'prevent-back'])->prefix('teacher')->name('teacher.')->group(function () {

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
    Route::post('/quizzes/assign', [TeacherQuizController::class, 'assign'])->name('quiz.assign');
    Route::delete('/quizzes/assignments/{assignment}', [TeacherQuizController::class, 'unassign'])->name('quiz.unassign');

    // AI Quiz (keep before wildcard {quiz})
    Route::get('/quizzes/ai', [TeacherAiQuizController::class, 'showUpload'])->name('quiz.ai');
    Route::post('/quizzes/ai', [TeacherAiQuizController::class, 'generate'])->name('quiz.ai.generate');
    Route::post('/quizzes/ai/topic', [TeacherAiQuizController::class, 'generateFromTopic'])->name('quiz.ai.topic');
    Route::post('/quizzes/ai/text', [TeacherAiQuizController::class, 'generateFromText'])->name('quiz.ai.text');
    Route::post('/quizzes/ai/pdf', [TeacherAiQuizController::class, 'generateFromPdf'])->name('quiz.ai.pdf');

    Route::get('/quizzes/{quiz}', [TeacherQuizController::class, 'show'])->name('quiz.show');
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quiz.destroy');

    // Profile
    Route::get('/profile', [TeacherprofileController::class, 'show'])->name('profile');
    Route::put('/profile', [TeacherprofileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [TeacherprofileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [TeacherprofileController::class, 'destroy'])->name('profile.destroy');
});

//acts as the "checker" that the frontend talks to while the user sees the loading screen.
Route::get('/api/check-new-deck/{oldId}', function ($oldId) {
    // Check if there is ANY deck created after the oldId for this user
    $newDeck = Auth::user()->decks()
        ->where('id', '>', $oldId)
        ->latest()
        ->first();

    return response()->json([
        'is_ready' => (bool) $newDeck,
        'deck_id' => $newDeck?->id
    ]);
})->middleware('auth');

Route::get('/api/check-new-quiz/{oldId}', function ($oldId) {
    $newQuiz = \App\Models\Quiz::where('teacher_id', Auth::id())
        ->where('id', '>', $oldId)
        ->latest()
        ->first();

    return response()->json([
        'is_ready' => (bool) $newQuiz,
        'quiz_id' => $newQuiz?->id
    ]);
})->middleware(['auth', 'teacher']);

require __DIR__.'/auth.php';
