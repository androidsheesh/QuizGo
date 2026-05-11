<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'is_shared',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    protected static function booted()
    {
        $clearCache = function($deck){
            Cache::forget("user_{$deck->user_id}_latest_decks");
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
