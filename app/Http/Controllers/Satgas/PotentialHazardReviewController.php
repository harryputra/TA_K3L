<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Hazards\UpdatePotentialHazardReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\UpdatePotentialHazardStatusRequest;
use App\Models\PotentialHazardReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PotentialHazardReviewController extends Controller
{
    public function __construct(
        protected UpdatePotentialHazardReportStatus $updatePotentialHazardReportStatus,
    ) {
    }

    public function index(Request $request): View
    {
        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $reports = PotentialHazardReport::query()
            ->with(['reporter', 'location'])
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('report_number', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('title', 'like', '%' . $selectedQuery . '%')
                        ->orWhereHas('reporter', fn ($reporterQuery) => $reporterQuery->where('name', 'like', '%' . $selectedQuery . '%'))
                        ->orWhereHas('location', fn ($locationQuery) => $locationQuery->where('name', 'like', '%' . $selectedQuery . '%'));
                });
            })
            ->when($selectedStatus !== '', fn ($query) => $query->where('status', $selectedStatus))
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        $summaryCounts = collect(['submitted', 'reviewed', 'resolved'])
            ->mapWithKeys(fn (string $status) => [
                $status => PotentialHazardReport::query()->where('status', $status)->count(),
            ])
            ->all();

        return view('satgas.hazards.index', compact('reports', 'summaryCounts', 'selectedQuery', 'selectedStatus'));
    }

    public function show(PotentialHazardReport $potentialHazardReport): View
    {
        $potentialHazardReport->load([
            'reporter.role',
            'location',
            'attachments',
            'reviewer',
            'resolver',
        ]);

        $statusOptions = collect(
            $this->updatePotentialHazardReportStatus->allowedTransitions($potentialHazardReport->status)
        )->mapWithKeys(fn (string $status) => [$status => ucfirst($status)])->all();

        return view('satgas.hazards.show', [
            'hazardReport' => $potentialHazardReport,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function updateStatus(
        UpdatePotentialHazardStatusRequest $request,
        PotentialHazardReport $potentialHazardReport,
    ): RedirectResponse {
        $this->updatePotentialHazardReportStatus->handle(
            $potentialHazardReport,
            $request->string('status')->toString(),
            $request->string('response_note')->toString(),
            $request->user()->id,
        );

        return redirect()
            ->route('satgas.hazards.show', $potentialHazardReport)
            ->with('status', 'Status hazard report berhasil diperbarui.');
    }
}
