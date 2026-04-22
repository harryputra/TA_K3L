<?php

namespace App\Policies;

use App\Models\IncidentReport;
use App\Models\User;

class IncidentReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, IncidentReport $incidentReport): bool
    {
        if ($user->isAdmin() || $user->isSatgas()) {
            return true;
        }

        return $incidentReport->reported_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function verify(User $user, IncidentReport $incidentReport): bool
    {
        if (! ($user->isAdmin() || $user->isSatgas())) {
            return false;
        }

        return in_array($incidentReport->status, ['submitted', 'investigating'], true);
    }
}
