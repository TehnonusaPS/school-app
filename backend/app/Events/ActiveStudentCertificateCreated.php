<?php

namespace App\Events;

use App\Models\ActiveStudentCertificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveStudentCertificateCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certificate;

    /**
     * Create a new event instance.
     */
    public function __construct(ActiveStudentCertificate $certificate)
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
            'id'            => $this->certificate->id,
            'studentId'     => $this->certificate->student_id,
            'academicYearId'=> $this->certificate->academic_year_id,
            'nama'          => $this->certificate->nama,
            'nisn'          => $this->certificate->nisn,
            'kelas'         => $this->certificate->kelas,
            'tanggalLahir'  => $this->certificate->tanggal_lahir ? $this->certificate->tanggal_lahir->format('Y-m-d') : '',
            'alamat'        => $this->certificate->alamat,
            'tahunAkademik' => $this->certificate->academicYear ? $this->certificate->academicYear->name : '',
            'semester'      => $this->certificate->semester,
            'status'        => $this->certificate->status,
            'tanggalDibuat' => $this->certificate->tanggal_dibuat ? $this->certificate->tanggal_dibuat->format('Y-m-d') : '',
        ];
    }
}
