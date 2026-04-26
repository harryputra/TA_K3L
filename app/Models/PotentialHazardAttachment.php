<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotentialHazardAttachment extends Model
{
    protected $fillable = [
        'potential_hazard_report_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(PotentialHazardReport::class, 'potential_hazard_report_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
