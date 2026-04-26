<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedQuery = trim((string) $request->string('q'));
        $selectedReadStatus = trim((string) $request->string('read_status', 'all'));
        $selectedType = trim((string) $request->string('type', 'all'));

        $activities = ActivityLog::query()
            ->with(['actor', 'subject'])
            ->where('user_id', $request->user()->id)
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('title', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('description', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('type', 'like', '%' . $selectedQuery . '%');
                });
            })
            ->when($selectedReadStatus === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($selectedReadStatus === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->when($selectedType === 'incident', fn ($query) => $query->where('type', 'like', 'incident_%'))
            ->when($selectedType === 'hazard', fn ($query) => $query->where('type', 'like', 'hazard_%'))
            ->when($selectedType === 'system', fn ($query) => $query
                ->where('type', 'not like', 'incident_%')
                ->where('type', 'not like', 'hazard_%'))
            ->orderByDesc('occurred_at')
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'total' => ActivityLog::query()->where('user_id', $request->user()->id)->count(),
            'unread' => ActivityLog::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count(),
            'incident_related' => ActivityLog::query()
                ->where('user_id', $request->user()->id)
                ->where('type', 'like', 'incident_%')
                ->count(),
            'hazard_related' => ActivityLog::query()
                ->where('user_id', $request->user()->id)
                ->where('type', 'like', 'hazard_%')
                ->count(),
        ];

        return view('user.activities.index', compact(
            'activities',
            'summary',
            'selectedQuery',
            'selectedReadStatus',
            'selectedType',
        ));
    }

    public function markRead(Request $request, ActivityLog $activityLog): RedirectResponse
    {
        abort_unless((int) $activityLog->user_id === (int) $request->user()->id, 403);

        if ($activityLog->read_at === null) {
            $activityLog->forceFill([
                'read_at' => now(),
            ])->save();
        }

        return redirect()
            ->route('user.activities.index')
            ->with('status', 'Aktivitas ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        ActivityLog::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('user.activities.index')
            ->with('status', 'Semua aktivitas ditandai sudah dibaca.');
    }
}
