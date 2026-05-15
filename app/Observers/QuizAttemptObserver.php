<?php

namespace App\Observers;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Cache;

class QuizAttemptObserver
{
    /**
     * Handle the QuizAttempt "saved" event.
     * This fires when a student finishes an attempt or a score is updated.
     */
    public function saved(QuizAttempt $attempt): void
    {
        $this->clearTeacherCache($attempt);
    }

    /**
     * Handle the QuizAttempt "deleted" event.
     */
    public function deleted(QuizAttempt $attempt): void
    {
        $this->clearTeacherCache($attempt);
    }

    /**
     * Traverse the relationships in your model to find the teacher's ID
     */
    private function clearTeacherCache(QuizAttempt $attempt): void
    {
        // Using the exact relationships you defined in your model:
        // QuizAttempt -> belongsTo -> QuizAssignment -> belongsTo -> Quiz -> teacher_id
        $teacherId = $attempt->quizAssignment->quiz->teacher_id ?? null;

        if ($teacherId) {
            Cache::tags(["teacher:{$teacherId}:dashboard"])->flush();
        }
    }
}
