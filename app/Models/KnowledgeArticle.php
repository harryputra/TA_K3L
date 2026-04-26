<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeArticle extends Model
{
    use SoftDeletes;

    public const CONTENT_SCHEMA = 'knowledge_sections_v1';

    protected $fillable = [
        'knowledge_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'reading_time',
        'video_url',
        'thumbnail_path',
        'status',
        'created_by',
        'approved_by',
        'published_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'knowledge_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(KnowledgeAttachment::class);
    }

    public function structuredSections(): array
    {
        $decoded = json_decode((string) $this->content, true);

        if (
            json_last_error() === JSON_ERROR_NONE
            && is_array($decoded)
            && ($decoded['schema'] ?? null) === self::CONTENT_SCHEMA
            && is_array($decoded['sections'] ?? null)
        ) {
            return collect($decoded['sections'])
                ->map(fn ($section, $index) => $this->normalizeSection($section, $index))
                ->filter(fn (array $section) => $section['title'] !== '' || $section['body'] !== '' || $section['media_type'] !== 'none')
                ->values()
                ->all();
        }

        return [[
            'id' => 'legacy-1',
            'title' => 'Ringkasan Materi',
            'body' => (string) $this->content,
            'list_style' => 'bullet',
            'media_type' => 'none',
            'media_path' => null,
            'media_url' => $this->video_url,
            'caption' => null,
        ]];
    }

    public static function encodeStructuredSections(array $sections): string
    {
        return json_encode([
            'schema' => self::CONTENT_SCHEMA,
            'sections' => array_values($sections),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function normalizeSection(mixed $section, int $index): array
    {
        $section = is_array($section) ? $section : [];

        return [
            'id' => (string) ($section['id'] ?? 'section-' . ($index + 1)),
            'title' => trim((string) ($section['title'] ?? '')),
            'body' => trim((string) ($section['body'] ?? '')),
            'list_style' => in_array(($section['list_style'] ?? 'paragraph'), ['paragraph', 'dash', 'bullet', 'number'], true)
                ? $section['list_style']
                : 'paragraph',
            'media_type' => in_array(($section['media_type'] ?? 'none'), ['none', 'image', 'video'], true)
                ? $section['media_type']
                : 'none',
            'media_path' => $section['media_path'] ?? null,
            'media_url' => $section['media_url'] ?? null,
            'caption' => trim((string) ($section['caption'] ?? '')),
        ];
    }
}
