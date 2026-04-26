<?php

namespace Tests\Feature;

use App\Models\EmergencyContact;
use App\Models\EmergencyResponseStep;
use App\Models\FirstAidGuide;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmergencyContentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_emergency_contact(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->post(route('admin.emergency-contacts.store'), [
                'name' => 'Pos Keamanan Kampus',
                'phone_number' => '113',
                'description' => 'Kontak untuk situasi awal di area kampus.',
                'icon' => 'shield-check',
                'color_class' => 'bg-emerald-100 text-emerald-700',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.emergency-contacts.index'))
            ->assertSessionHas('status', 'Kontak darurat berhasil ditambahkan.');

        $contact = EmergencyContact::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.emergency-contacts.update', $contact), [
                'name' => 'Pos Keamanan Utama',
                'phone_number' => '114',
                'description' => 'Kontak keamanan utama kampus.',
                'icon' => 'phone',
                'color_class' => 'bg-cyan-100 text-cyan-700',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('admin.emergency-contacts.index'))
            ->assertSessionHas('status', 'Kontak darurat berhasil diperbarui.');

        $this->assertDatabaseHas('emergency_contacts', [
            'id' => $contact->id,
            'name' => 'Pos Keamanan Utama',
            'phone_number' => '114',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.emergency-contacts.destroy', $contact))
            ->assertRedirect(route('admin.emergency-contacts.index'))
            ->assertSessionHas('status', 'Kontak darurat berhasil dihapus.');

        $this->assertDatabaseMissing('emergency_contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_emergency_response_step(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->post(route('admin.emergency-response-steps.store'), [
                'title' => 'Amankan Area',
                'description' => 'Pastikan area berbahaya segera diamankan.',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.emergency-response-steps.index'))
            ->assertSessionHas('status', 'Langkah tanggap cepat berhasil ditambahkan.');

        $step = EmergencyResponseStep::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.emergency-response-steps.update', $step), [
                'title' => 'Isolasi Area',
                'description' => 'Batasi akses ke area hingga aman.',
                'sort_order' => 3,
            ])
            ->assertRedirect(route('admin.emergency-response-steps.index'))
            ->assertSessionHas('status', 'Langkah tanggap cepat berhasil diperbarui.');

        $this->assertDatabaseHas('emergency_response_steps', [
            'id' => $step->id,
            'title' => 'Isolasi Area',
            'sort_order' => 3,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.emergency-response-steps.destroy', $step))
            ->assertRedirect(route('admin.emergency-response-steps.index'))
            ->assertSessionHas('status', 'Langkah tanggap cepat berhasil dihapus.');

        $this->assertDatabaseMissing('emergency_response_steps', [
            'id' => $step->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_first_aid_guide_with_actions(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->post(route('admin.first-aid-guides.store'), [
                'title' => 'Luka Ringan',
                'icon' => 'bandage',
                'accent_class' => 'bg-amber-100 text-amber-700',
                'summary' => 'Panduan cepat untuk luka ringan.',
                'sort_order' => 1,
                'is_active' => '1',
                'actions_text' => "Cuci luka dengan air bersih\nTutupi dengan kasa steril",
            ])
            ->assertRedirect(route('admin.first-aid-guides.index'))
            ->assertSessionHas('status', 'Panduan pertolongan pertama berhasil ditambahkan.');

        $guide = FirstAidGuide::query()->with('actions')->firstOrFail();
        $this->assertCount(2, $guide->actions);

        $this->actingAs($admin)
            ->put(route('admin.first-aid-guides.update', $guide), [
                'title' => 'Luka Ringan Terkini',
                'icon' => 'first-aid',
                'accent_class' => 'bg-rose-100 text-rose-700',
                'summary' => 'Panduan yang sudah diperbarui.',
                'sort_order' => 2,
                'actions_text' => "Hentikan perdarahan ringan\nBersihkan luka\nTutupi luka",
            ])
            ->assertRedirect(route('admin.first-aid-guides.index'))
            ->assertSessionHas('status', 'Panduan pertolongan pertama berhasil diperbarui.');

        $guide->refresh()->load('actions');

        $this->assertSame('Luka Ringan Terkini', $guide->title);
        $this->assertFalse($guide->is_active);
        $this->assertCount(3, $guide->actions);
        $this->assertSame('Hentikan perdarahan ringan', $guide->actions[0]->description);

        $this->actingAs($admin)
            ->delete(route('admin.first-aid-guides.destroy', $guide))
            ->assertRedirect(route('admin.first-aid-guides.index'))
            ->assertSessionHas('status', 'Panduan pertolongan pertama berhasil dihapus.');

        $this->assertDatabaseMissing('first_aid_guides', [
            'id' => $guide->id,
        ]);
        $this->assertDatabaseMissing('first_aid_actions', [
            'first_aid_guide_id' => $guide->id,
        ]);
    }

    protected function createAdminUser(): User
    {
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        return User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }
}
