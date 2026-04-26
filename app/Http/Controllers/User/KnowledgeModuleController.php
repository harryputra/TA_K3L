<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\View\View;

class KnowledgeModuleController extends Controller
{
    public function __invoke(string $slug): View
    {
        if (! Schema::hasTable('knowledge_articles')) {
            throw new NotFoundHttpException();
        }

        $article = KnowledgeArticle::query()
            ->with(['category', 'attachments'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedModules = KnowledgeArticle::query()
            ->with('category')
            ->where('status', 'published')
            ->whereKeyNot($article->id)
            ->when($article->knowledge_category_id, fn ($query) => $query->where('knowledge_category_id', $article->knowledge_category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('user.knowledge.show', compact('article', 'relatedModules'));
    }
}
