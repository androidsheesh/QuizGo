<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Double submissions already in the table would block the unique index; keep each student's first attempt
        $duplicates = DB::table('quiz_attempts')
            ->select('student_id', 'quiz_assignment_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('student_id', 'quiz_assignment_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('quiz_attempts')
                ->where('student_id', $duplicate->student_id)
                ->where('quiz_assignment_id', $duplicate->quiz_assignment_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unique(['student_id', 'quiz_assignment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'quiz_assignment_id']);
        });
    }
};
