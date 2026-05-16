<?php

namespace App\Events;

use App\Models\FlashcardGeneration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlashcardGenerationFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FlashcardGeneration $generation)
    {
        $this->generation->loadMissing('deck');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->generation->user_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'flashcard.generation.finished';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->generation->uuid,
            'status' => $this->generation->status,
            'deck_id' => $this->generation->deck_id,
            'deck_title' => $this->generation->deck?->title,
            'deck_url' => $this->generation->deck_id
                ? route('decks.show', $this->generation->deck_id)
                : null,
            'error' => $this->generation->status === FlashcardGeneration::STATUS_FAILED
                ? [
                    'code' => $this->generation->error_code,
                    'message' => 'Generation failed. Please try a different prompt or try again later.',
                ]
                : null,
        ];
    }
}
