<?php

namespace App\Observers;

use App\Models\QuizAssignment;
use Illuminate\Support\Facades\Cache;

class QuizAssignmentObserver
{
    /**
     * Handle the QuizAssignment "saved" event.
     */
    public function saved(QuizAssignment $assignment): void
    {
        $this->clearTeacherCache($assignment);
    }

    /**
     * Handle the QuizAssignment "deleted" event.
     */
    public function deleted(QuizAssignment $assignment): void
    {
        $this->clearTeacherCache($assignment);
    }

    /**
     * Traverse back to the quiz to find the teacher's ID
     */
    private function clearTeacherCache(QuizAssignment $assignment): void
    {
        $teacherId = $assignment->quiz?->teacher_id;

        if ($teacherId) {
            Cache::tags(["teacher:{$teacherId}:dashboard"])->flush();
        }
    }
}
