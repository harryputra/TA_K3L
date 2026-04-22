<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hiradc extends Model
{
    protected $fillable = [
        'document_number',
        'title',
        'department_id',
        'location_id',
        'main_activity',
        'person_in_charge_id',
        'created_by',
        'reviewed_by',
        'approved_by',
        'status',
        'revision_number',
        'notes',
        'submitted_at',
        'reviewed_at',
        'approved_at',
    ];
}
