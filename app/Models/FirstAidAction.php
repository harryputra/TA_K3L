<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirstAidAction extends Model
{
    protected $fillable = [
        'first_aid_guide_id',
        'description',
        'sort_order',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(FirstAidGuide::class, 'first_aid_guide_id');
    }
}
