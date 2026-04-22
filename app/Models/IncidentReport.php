<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_number',
        'reported_by',
        'victim_user_id',
        'incident_category_id',
        'injury_category_id',
        'body_part_id',
        'location_id',
        'incident_date',
        'incident_time',
        'severity_level',
        'title',
        'chronology',
        'cause',
        'initial_action',
        'impact',
        'status',
        'assigned_satgas_id',
        'verified_by',
        'closed_by',
        'submitted_at',
        'verified_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function victim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'victim_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'incident_category_id');
    }

    public function injuryCategory(): BelongsTo
    {
        return $this->belongsTo(InjuryCategory::class);
    }

    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedSatgas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_satgas_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(IncidentStatusHistory::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(IncidentFollowUp::class);
    }
}
