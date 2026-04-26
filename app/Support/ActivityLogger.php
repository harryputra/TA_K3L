<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(
        int $userId,
        ?int $actorId,
        string $type,
        string $title,
        ?string $description = null,
        ?Model $subject = null,
        array $metadata = [],
    ): ActivityLog {
        return ActivityLog::query()->create([
            'user_id' => $userId,
            'actor_id' => $actorId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}
