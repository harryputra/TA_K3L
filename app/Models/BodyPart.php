<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyPart extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function incidentInjuries(): HasMany
    {
        return $this->hasMany(IncidentInjury::class);
    }
}
