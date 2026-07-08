<?php

namespace App\Events;

use App\Models\StudentDispensationCertificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentDispensationCertificateCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certificate;

    /**
     * Create a new event instance.
     */
    public function __construct(StudentDispensationCertificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('persuratan.' . $this->certificate->school_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // Load relation students if not loaded
        $this->certificate->load('students');

        $siswa = $this->certificate->students->map(function ($s) {
            return [
                'nama'  => $s->nama,
                'nisn'  => $s->nisn,
                'kelas' => $s->kelas,
            ];
        })->toArray();

        return [
            'id'            => $this->certificate->id,
            'tanggalDibuat' => $this->certificate->tanggal_dibuat ? $this->certificate->tanggal_dibuat->format('Y-m-d') : '',
            'tanggalAwal'   => $this->certificate->tanggal_awal ? $this->certificate->tanggal_awal->format('Y-m-d') : '',
            'tanggalAkhir'  => $this->certificate->tanggal_akhir ? $this->certificate->tanggal_akhir->format('Y-m-d') : '',
            'perihal'       => $this->certificate->perihal,
            'status'        => $this->certificate->status,
            'siswa'         => $siswa,
        ];
    }
}
