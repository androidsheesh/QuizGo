<?php

namespace App\Observers;

use App\Models\Quiz;
use Illuminate\Support\Facades\Cache;

class QuizObserver
{
    /**
     * Handle the Quiz "saved" event (covers created and updated).
     */
    public function saved(Quiz $quiz): void
    {
        $this->clearTeacherCache($quiz);
    }

    /**
     * Handle the Quiz "deleted" event.
     */
    public function deleted(Quiz $quiz): void
    {
        $this->clearTeacherCache($quiz);
    }

    /**
     * Reusable cache clearing logic
     */
    private function clearTeacherCache(Quiz $quiz): void
    {
        // Because the Quiz model has the teacher_id, we can grab it directly
        Cache::tags(["teacher:{$quiz->teacher_id}:dashboard"])->flush();
    }
}
