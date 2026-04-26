<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyResponseStep extends Model
{
    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
