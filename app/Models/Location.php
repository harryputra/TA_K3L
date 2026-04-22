<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function dailyChecks(): HasMany
    {
        return $this->hasMany(DailyCheck::class);
    }

    public function hiradcs(): HasMany
    {
        return $this->hasMany(Hiradc::class);
    }
}
