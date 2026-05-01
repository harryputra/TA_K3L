<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PotentialHazardReport extends Model
{
    protected $fillable = [
        'report_number',
        'reported_by',
        'reporter_name',
        'reporter_email',
        'reporter_whatsapp',
        'reviewed_by',
        'resolved_by',
        'location_id',
        'hazard_type',
        'title',
        'specific_location',
        'notes',
        'response_note',
        'status',
        'submitted_at',
        'reviewed_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PotentialHazardAttachment::class);
    }
}
