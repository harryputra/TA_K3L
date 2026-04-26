<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKnowledgeCategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_knowledge_category(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->post(route('admin.knowledge-categories.store'), [
                'name' => 'Keselamatan Dasar',
                'description' => 'Materi dasar keselamatan kerja.',
            ])
            ->assertRedirect(route('admin.knowledge-categories.index'))
            ->assertSessionHas('status', 'Kategori knowledge berhasil ditambahkan.');

        $category = KnowledgeCategory::query()->firstOrFail();
        $this->assertSame('keselamatan-dasar', $category->slug);

        $this->actingAs($admin)
            ->put(route('admin.knowledge-categories.update', $category), [
                'name' => 'Keselamatan Dasar Revisi',
                'slug' => 'kategori-revisi',
                'description' => 'Deskripsi revisi.',
            ])
            ->assertRedirect(route('admin.knowledge-categories.index'))
            ->assertSessionHas('status', 'Kategori knowledge berhasil diperbarui.');

        $this->assertDatabaseHas('knowledge_categories', [
            'id' => $category->id,
            'name' => 'Keselamatan Dasar Revisi',
            'slug' => 'kategori-revisi',
        ]);
    }

    public function test_admin_cannot_delete_knowledge_category_that_is_still_used(): void
    {
        $admin = $this->createAdminUser();

        $category = KnowledgeCategory::query()->create([
            'name' => 'Pelaporan',
            'slug' => 'pelaporan',
        ]);

        KnowledgeArticle::query()->create([
            'knowledge_category_id' => $category->id,
            'title' => 'Materi Pelaporan',
            'slug' => 'materi-pelaporan',
            'content' => 'Isi materi.',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.knowledge-categories.destroy', $category))
            ->assertRedirect(route('admin.knowledge-categories.index'))
            ->assertSessionHasErrors('knowledge_category');

        $this->assertDatabaseHas('knowledge_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_can_delete_unused_knowledge_category(): void
    {
        $admin = $this->createAdminUser();

        $category = KnowledgeCategory::query()->create([
            'name' => 'Toolbox Meeting',
            'slug' => 'toolbox-meeting',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.knowledge-categories.destroy', $category))
            ->assertRedirect(route('admin.knowledge-categories.index'))
            ->assertSessionHas('status', 'Kategori knowledge berhasil dihapus.');

        $this->assertDatabaseMissing('knowledge_categories', [
            'id' => $category->id,
        ]);
    }

    protected function createAdminUser(): User
    {
        $adminRole = Role::query()->create(['name' => 'Admin', 'code' => 'admin']);

        return User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }
}
