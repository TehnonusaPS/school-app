<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'foundation_id',
        'school_id',
        'phone',
        'photo',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    //  Relationships
    // ──────────────────────────────────────────────

    /**
     * Get the role of this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the foundation this user belongs to (for admin_yayasan).
     */
    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }

    /**
     * Get the school this user belongs to (for kepala_sekolah, admin_sekolah, tata_usaha, guru, wali_kelas).
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get teacher/staff profile (for guru, wali_kelas, kepala_sekolah, tata_usaha, admin_sekolah).
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Get student profile (for siswa).
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Get parent profile (for orang_tua).
     */
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    // ──────────────────────────────────────────────
    //  Helper Methods
    // ──────────────────────────────────────────────

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Check if the user is a superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }
}
