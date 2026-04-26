<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKnowledgeCategoryRequest;
use App\Http\Requests\Admin\UpdateKnowledgeCategoryRequest;
use App\Models\KnowledgeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KnowledgeCategoryController extends Controller
{
    public function index(): View
    {
        $categories = KnowledgeCategory::query()
            ->withCount('articles')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.knowledge-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.knowledge-categories.create');
    }

    public function store(StoreKnowledgeCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        KnowledgeCategory::query()->create([
            ...$validated,
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? null, $validated['name']),
        ]);

        return redirect()
            ->route('admin.knowledge-categories.index')
            ->with('status', 'Kategori knowledge berhasil ditambahkan.');
    }

    public function edit(KnowledgeCategory $knowledgeCategory): View
    {
        return view('admin.knowledge-categories.edit', compact('knowledgeCategory'));
    }

    public function update(UpdateKnowledgeCategoryRequest $request, KnowledgeCategory $knowledgeCategory): RedirectResponse
    {
        $validated = $request->validated();

        $knowledgeCategory->update([
            ...$validated,
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? null, $validated['name'], $knowledgeCategory->id),
        ]);

        return redirect()
            ->route('admin.knowledge-categories.index')
            ->with('status', 'Kategori knowledge berhasil diperbarui.');
    }

    public function destroy(KnowledgeCategory $knowledgeCategory): RedirectResponse
    {
        if ($knowledgeCategory->articles()->exists()) {
            return redirect()
                ->route('admin.knowledge-categories.index')
                ->withErrors([
                    'knowledge_category' => 'Kategori knowledge tidak bisa dihapus karena masih dipakai oleh materi.',
                ]);
        }

        $knowledgeCategory->delete();

        return redirect()
            ->route('admin.knowledge-categories.index')
            ->with('status', 'Kategori knowledge berhasil dihapus.');
    }

    protected function generateUniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $name);
        $candidate = $baseSlug !== '' ? $baseSlug : 'kategori-knowledge';
        $counter = 1;

        while (
            KnowledgeCategory::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
