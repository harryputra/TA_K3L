<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentInjury extends Model
{
    protected $fillable = [
        'incident_report_id',
        'injury_category_id',
        'body_part_id',
        'description',
    ];

    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    public function injuryCategory(): BelongsTo
    {
        return $this->belongsTo(InjuryCategory::class);
    }

    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class);
    }
}
