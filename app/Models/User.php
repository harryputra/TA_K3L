<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'phone',
        'password',
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
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'reported_by');
    }

    public function assignedIncidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'assigned_satgas_id');
    }

    public function createdKnowledgeArticles(): HasMany
    {
        return $this->hasMany(KnowledgeArticle::class, 'created_by');
    }

    public function createdHiradcs(): HasMany
    {
        return $this->hasMany(Hiradc::class, 'created_by');
    }

    public function dailyChecks(): HasMany
    {
        return $this->hasMany(DailyCheck::class, 'inspected_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->code === 'admin';
    }

    public function isSatgas(): bool
    {
        return $this->role?->code === 'satgas';
    }

    public function isMahasiswa(): bool
    {
        return $this->role?->code === 'mahasiswa';
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role?->code) {
            'admin' => 'admin.dashboard',
            'satgas' => 'satgas.dashboard',
            default => 'user.dashboard',
        };
    }
}
