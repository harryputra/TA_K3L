<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFirstAidGuideRequest;
use App\Http\Requests\Admin\UpdateFirstAidGuideRequest;
use App\Models\FirstAidGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FirstAidGuideController extends Controller
{
    public function index(): View
    {
        $guides = FirstAidGuide::query()
            ->withCount('actions')
            ->orderBy('sort_order')
            ->paginate(10);

        return view('admin.first-aid-guides.index', compact('guides'));
    }

    public function create(): View
    {
        return view('admin.first-aid-guides.create');
    }

    public function store(StoreFirstAidGuideRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $guide = FirstAidGuide::query()->create([
                ...$request->safe()->except('actions_text'),
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->syncActions($guide, $request->string('actions_text')->toString());
        });

        return redirect()
            ->route('admin.first-aid-guides.index')
            ->with('status', 'Panduan pertolongan pertama berhasil ditambahkan.');
    }

    public function edit(FirstAidGuide $firstAidGuide): View
    {
        $firstAidGuide->load('actions');

        return view('admin.first-aid-guides.edit', compact('firstAidGuide'));
    }

    public function update(UpdateFirstAidGuideRequest $request, FirstAidGuide $firstAidGuide): RedirectResponse
    {
        DB::transaction(function () use ($request, $firstAidGuide) {
            $firstAidGuide->update([
                ...$request->safe()->except('actions_text'),
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->syncActions($firstAidGuide, $request->string('actions_text')->toString());
        });

        return redirect()
            ->route('admin.first-aid-guides.index')
            ->with('status', 'Panduan pertolongan pertama berhasil diperbarui.');
    }

    public function destroy(FirstAidGuide $firstAidGuide): RedirectResponse
    {
        $firstAidGuide->delete();

        return redirect()
            ->route('admin.first-aid-guides.index')
            ->with('status', 'Panduan pertolongan pertama berhasil dihapus.');
    }

    protected function syncActions(FirstAidGuide $guide, string $actionsText): void
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', $actionsText) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();

        $guide->actions()->delete();

        foreach ($lines as $index => $line) {
            $guide->actions()->create([
                'description' => $line,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
