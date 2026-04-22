<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheck extends Model
{
    protected $fillable = [
        'check_date',
        'location_id',
        'inspected_by',
        'status',
        'general_notes',
        'reviewed_by',
        'reviewed_at',
    ];
}
