<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Throwable;

/**
 * One background AI generation (a flashcard deck or a quiz) and its progress.
 */
class Generation extends Model
{
    use HasFactory;

    public const KIND_FLASHCARDS = 'flashcards';
    public const KIND_QUIZ = 'quiz';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'uuid',
        'kind',
        'user_id',
        'deck_id',
        'quiz_id',
        'source_type',
        'status',
        'error_code',
        'error_message',
        'completed_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Generation $generation) {
            if (!$generation->uuid) {
                $generation->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Where the finished deck or quiz can be opened, once there is one.
     */
    public function resultUrl(): ?string
    {
        return match (true) {
            $this->deck_id !== null => route('decks.show', $this->deck_id),
            $this->quiz_id !== null => route('teacher.quiz.show', $this->quiz_id),
            default => null,
        };
    }

    public function markCompleted(Deck|Quiz $result): void
    {
        $this->forceFill([
            'deck_id' => $result instanceof Deck ? $result->id : null,
            'quiz_id' => $result instanceof Quiz ? $result->id : null,
            'status' => self::STATUS_COMPLETED,
            'error_code' => null,
            'error_message' => null,
            'completed_at' => now(),
            'failed_at' => null,
        ])->save();
    }

    public function markFailed(Throwable $exception, string $errorCode = 'GENERATION_FAILED'): void
    {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_FAILED) {
            return;
        }

        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'error_code' => $errorCode,
            'error_message' => Str::limit($exception->getMessage(), 1000),
            'failed_at' => now(),
        ])->save();
    }
}
