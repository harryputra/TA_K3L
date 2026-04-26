<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeCenterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedQuery = trim((string) $request->string('q'));
        $selectedCategory = trim((string) $request->string('category'));
        $featuredArticles = collect();
        $latestArticles = collect();
        $categories = collect();

        if (
            Schema::hasTable('knowledge_articles') &&
            Schema::hasTable('knowledge_categories')
        ) {
            $baseQuery = KnowledgeArticle::query()
                ->with('category')
                ->where('status', 'published')
                ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                    $query->where(function ($subQuery) use ($selectedQuery) {
                        $subQuery
                            ->where('title', 'like', '%' . $selectedQuery . '%')
                            ->orWhere('summary', 'like', '%' . $selectedQuery . '%')
                            ->orWhere('content', 'like', '%' . $selectedQuery . '%');
                    });
                })
                ->when($selectedCategory !== '', function ($query) use ($selectedCategory) {
                    $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', $selectedCategory));
                });

            $featuredArticles = (clone $baseQuery)
                ->latest('published_at')
                ->take(3)
                ->get();

            $latestArticles = (clone $baseQuery)
                ->latest('published_at')
                ->get();

            $categories = KnowledgeCategory::query()
                ->withCount(['articles' => fn ($query) => $query->where('status', 'published')])
                ->orderBy('name')
                ->get();
        }

        $learningSteps = [
            ['title' => 'Kenali risiko area kerja', 'description' => 'Pelajari potensi bahaya sebelum memulai aktivitas di laboratorium, workshop, atau area umum.'],
            ['title' => 'Gunakan APD yang tepat', 'description' => 'Pastikan alat pelindung diri sesuai dengan jenis pekerjaan dan kondisi lingkungan.'],
            ['title' => 'Pahami prosedur darurat', 'description' => 'Ketahui jalur evakuasi, titik kumpul, dan kontak penting sebelum insiden terjadi.'],
            ['title' => 'Laporkan dan evaluasi', 'description' => 'Gunakan sistem pelaporan untuk mencatat kejadian dan mencegah insiden serupa berulang.'],
        ];

        return view('user.knowledge.index', compact(
            'featuredArticles',
            'latestArticles',
            'categories',
            'learningSteps',
            'selectedQuery',
            'selectedCategory',
        ));
    }
}
