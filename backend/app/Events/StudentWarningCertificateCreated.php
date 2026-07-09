<?php

namespace App\Events;

use App\Models\StudentWarningCertificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentWarningCertificateCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certificate;

    /**
     * Create a new event instance.
     */
    public function __construct(StudentWarningCertificate $certificate)
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
        return [
            'id'                 => $this->certificate->id,
            'tanggalDibuat'      => $this->certificate->tanggal_dibuat ? $this->certificate->tanggal_dibuat->format('Y-m-d') : '',
            'jenisSurat'         => $this->certificate->jenis_surat,
            'studentId'          => $this->certificate->student_id,
            'namaSiswa'          => $this->certificate->nama,
            'nisn'               => $this->certificate->nisn,
            'kelas'              => $this->certificate->kelas,
            'tanggal'            => $this->certificate->tanggal ? $this->certificate->tanggal->format('Y-m-d') : '',
            'perihalPelanggaran' => $this->certificate->perihal_pelanggaran,
            'jumlahTunggakan'    => $this->certificate->jumlah_tunggakan,
            'status'             => $this->certificate->status,
        ];
    }
}
