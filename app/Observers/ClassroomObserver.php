<?php

namespace App\Observers;

use App\Models\Classroom;
use Illuminate\Support\Facades\Cache;

class ClassroomObserver
{
    /**
     * Handle the Classroom "saved" event (covers created and updated).
     */
    public function saved(Classroom $classroom): void
    {
        $this->clearTeacherCache($classroom);
    }

    /**
     * Handle the Classroom "deleted" event.
     */
    public function deleted(Classroom $classroom): void
    {
        $this->clearTeacherCache($classroom);
    }

    /**
     * Reusable cache clearing logic
     */
    private function clearTeacherCache(Classroom $classroom): void
    {
        Cache::tags(["teacher:{$classroom->teacher_id}:dashboard"])->flush();
    }
}
