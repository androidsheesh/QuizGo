<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'quiz_assignment_id',
        'score',
        'total_questions',
        'time_taken',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * The student who made this attempt.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The quiz assignment this attempt is for.
     */
    public function quizAssignment(): BelongsTo
    {
        return $this->belongsTo(QuizAssignment::class);
    }

    /**
     * Individual answers in this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    /**
     * Get the score percentage.
     */
    public function getScorePercentageAttribute(): float
    {
        if ($this->total_questions === 0) return 0;
        return round(($this->score / $this->total_questions) * 100, 1);
    }
}
