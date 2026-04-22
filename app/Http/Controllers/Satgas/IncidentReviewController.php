<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Incidents\VerifyIncidentReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\VerifyIncidentReportRequest;
use App\Models\IncidentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IncidentReviewController extends Controller
{
    public function __construct(
        protected VerifyIncidentReport $verifyIncidentReport,
    ) {
    }

    public function index(): View
    {
        $reports = IncidentReport::query()
            ->with(['category', 'location', 'reporter'])
            ->latest()
            ->paginate(12);

        return view('satgas.incidents.index', compact('reports'));
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

        return view('satgas.incidents.show', compact('incidentReport'));
    }

    public function verify(VerifyIncidentReportRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $incidentReport = $this->verifyIncidentReport->handle(
            $incidentReport,
            $request->user()->id,
            $request->string('verification_note')->toString() ?: null,
        );

        return redirect()
            ->route('satgas.incidents.show', $incidentReport)
            ->with('status', "Laporan {$incidentReport->report_number} berhasil diverifikasi.");
    }
}
