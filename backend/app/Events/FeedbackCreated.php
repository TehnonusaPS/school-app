<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Feedback;

class FeedbackCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feedback;

    /**
     * Create a new event instance.
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('feedback.' . $this->feedback->school_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'             => $this->feedback->id,
            'judul'          => $this->feedback->title,
            'pesan'          => $this->feedback->content,
            'kategori'       => $this->feedback->category,
            'kelas'          => $this->feedback->classroom,
            'tanggal'        => $this->feedback->created_at->toISOString(),
            'school_id'      => $this->feedback->school_id,
            'foundation_id'  => $this->feedback->foundation_id,
        ];
    }
}
