<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeCenterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_published_knowledge_articles(): void
    {
        $user = $this->createMahasiswaUser();
        $category = KnowledgeCategory::query()->create([
            'name' => 'APD dan Peralatan',
            'slug' => 'apd-dan-peralatan',
        ]);

        KnowledgeArticle::query()->create([
            'knowledge_category_id' => $category->id,
            'title' => 'Panduan APD Bengkel',
            'slug' => 'panduan-apd-bengkel',
            'summary' => 'Ringkasan penggunaan APD.',
            'content' => 'Isi materi APD bengkel.',
            'reading_time' => '5 menit',
            'status' => 'published',
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.knowledge.index'))
            ->assertOk()
            ->assertSeeText('Panduan APD Bengkel')
            ->assertSeeText('1 Module Tersedia');
    }

    public function test_user_can_view_knowledge_module_detail(): void
    {
        $user = $this->createMahasiswaUser();
        $category = KnowledgeCategory::query()->create([
            'name' => 'Pelaporan Insiden',
            'slug' => 'pelaporan-insiden',
        ]);

        $article = KnowledgeArticle::query()->create([
            'knowledge_category_id' => $category->id,
            'title' => 'Cara Menulis Laporan Insiden',
            'slug' => 'cara-menulis-laporan-insiden',
            'summary' => 'Panduan menulis laporan yang jelas.',
            'content' => 'Tuliskan kronologi secara runtut dan faktual.',
            'reading_time' => '6 menit',
            'status' => 'published',
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.knowledge.show', $article->slug))
            ->assertOk()
            ->assertSeeText('Cara Menulis Laporan Insiden')
            ->assertSeeText('Tuliskan kronologi secara runtut dan faktual.');
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->create([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
