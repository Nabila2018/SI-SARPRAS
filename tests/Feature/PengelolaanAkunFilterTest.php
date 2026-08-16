<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengelolaanAkunFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $activeUser;
    private User $inactiveUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $this->staff = User::create([
            'email' => 'staff_filter@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Filter',
            'id_role' => $role->id_role,
            'status_akun' => 'Aktif',
        ]);

        $this->activeUser = User::create([
            'email' => 'active_user@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'User Aktif Uji',
            'id_role' => $role->id_role,
            'status_akun' => 'Aktif',
        ]);

        $this->inactiveUser = User::create([
            'email' => 'inactive_user@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'User Nonaktif Uji',
            'id_role' => $role->id_role,
            'status_akun' => 'Nonaktif',
        ]);
    }

    public function test_filter_by_status_aktif(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('staff.akun.index', ['status' => 'Aktif']));

        $response->assertStatus(200);
        $response->assertSee('User Aktif Uji');
        $response->assertDontSee('User Nonaktif Uji');
    }

    public function test_filter_by_status_nonaktif(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('staff.akun.index', ['status' => 'Nonaktif']));

        $response->assertStatus(200);
        $response->assertSee('User Nonaktif Uji');
        $response->assertDontSee('User Aktif Uji');
    }
}
