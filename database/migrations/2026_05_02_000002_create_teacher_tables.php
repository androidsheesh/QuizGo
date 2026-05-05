<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('classroom_student')) {
            Schema::create('classroom_student', function (Blueprint $table) {
                $table->id();
                $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['classroom_id', 'student_id']);
            });
        }

        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('time_limit')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
                $table->string('type');
                $table->text('question');
                $table->string('correct_answer');
                $table->json('choices')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('quiz_assignments')) {
            Schema::create('quiz_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
                $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
                $table->timestamp('assigned_at')->useCurrent();
                $table->timestamp('due_at')->nullable();
                $table->timestamps();
                $table->unique(['quiz_id', 'classroom_id']);
            });
        }

        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('quiz_assignment_id')->constrained('quiz_assignments')->cascadeOnDelete();
                $table->unsignedInteger('score')->default(0);
                $table->unsignedInteger('total_questions')->default(0);
                $table->unsignedInteger('time_taken')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('quiz_attempt_answers')) {
            Schema::create('quiz_attempt_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
                $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
                $table->text('student_answer')->nullable();
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_assignments');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('classroom_student');
        Schema::dropIfExists('classrooms');
    }
};
