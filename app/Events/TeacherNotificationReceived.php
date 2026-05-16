<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherNotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $teacherId,
        public string $title,
        public string $message,
        public string $type = 'success',
        public ?string $actionUrl = null,
        public string $actionLabel = 'Open',
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("teacher.{$this->teacherId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'teacher.notification.received';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
        ];
    }
}
