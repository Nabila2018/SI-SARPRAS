<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $this->user = User::create([
            'email' => 'original@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Nama Original',
            'id_role' => $role->id_role,
            'status_akun' => 'Aktif',
        ]);
    }

    public function test_user_can_update_nama_lengkap(): void
    {
        $this->actingAs($this->user);

        $response = $this->patch(route('profil.update'), [
            'nama_lengkap' => 'Nama Baru User',
        ]);

        $response->assertRedirect(route('profil.show'));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $this->user->refresh();
        $this->assertSame('Nama Baru User', $this->user->nama_lengkap);
        $this->assertSame('original@sisarpras.test', $this->user->email);
    }

    public function test_email_cannot_be_changed_even_if_request_is_manipulated(): void
    {
        $this->actingAs($this->user);

        $response = $this->patch(route('profil.update'), [
            'nama_lengkap' => 'Nama Update Terbaru',
            'email' => 'hacked@sisarpras.test',
        ]);

        $response->assertRedirect(route('profil.show'));

        $this->user->refresh();
        $this->assertSame('Nama Update Terbaru', $this->user->nama_lengkap);
        // Email must remain unchanged
        $this->assertSame('original@sisarpras.test', $this->user->email);
    }

    public function test_profile_page_displays_email_and_modal_does_not_contain_email_input(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('profil.show'));

        $response->assertStatus(200);
        $response->assertSee('original@sisarpras.test');
        $response->assertDontSee('name="email"', false);
    }
}
