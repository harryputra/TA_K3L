<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FirstAidGuide extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'accent_class',
        'summary',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function actions(): HasMany
    {
        return $this->hasMany(FirstAidAction::class)->orderBy('sort_order');
    }
}
