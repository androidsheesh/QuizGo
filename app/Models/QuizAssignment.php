<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'classroom_id',
        'assigned_at',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'due_at'      => 'datetime',
        ];
    }

    /**
     * The quiz being assigned.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * The classroom receiving the assignment.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Submitted student attempts on this assignment (in-progress attempts are excluded).
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class)->whereNotNull('completed_at');
    }

    public function isPastDue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }
}
