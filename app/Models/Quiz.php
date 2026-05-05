<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'time_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The teacher who created this quiz.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Questions belonging to this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Assignments of this quiz to classrooms.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(QuizAssignment::class);
    }
}
