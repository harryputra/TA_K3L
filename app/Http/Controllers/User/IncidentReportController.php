<?php

namespace App\Http\Controllers\User;

use App\Actions\Incidents\CreateIncidentReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\StoreIncidentReportRequest;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentReportController extends Controller
{
    public function __construct(
        protected CreateIncidentReport $createIncidentReport,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', IncidentReport::class);

        $reports = IncidentReport::query()
            ->with(['category', 'location'])
            ->where('reported_by', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('user.incidents.index', compact('reports'));
    }

    public function create(): View
    {
        $this->authorize('create', IncidentReport::class);

        return view('user.incidents.create', [
            'categories' => IncidentCategory::query()->orderBy('name')->get(),
            'locations' => Location::query()->where('is_active', true)->orderBy('name')->get(),
            'severityOptions' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
                'critical' => 'Kritis',
            ],
        ]);
    }

    public function show(IncidentReport $incidentReport): View
    {
        $this->authorize('view', $incidentReport);

        $incidentReport->load([
            'category',
            'location',
            'reporter',
            'victim',
            'attachments',
            'statusHistories.changer',
        ]);

        return view('user.incidents.show', compact('incidentReport'));
    }

    public function store(StoreIncidentReportRequest $request): RedirectResponse
    {
        $this->authorize('create', IncidentReport::class);

        $report = $this->createIncidentReport->handle(
            $request->safe()->except('victim_type'),
            $request->user()->id,
        );

        return redirect()
            ->route('user.incidents.index')
            ->with('status', "Laporan {$report->report_number} berhasil dikirim dan menunggu verifikasi Satgas.");
    }
}
