<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected array $permissions;
    protected array $roleData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->user = User::where('email', 'rifat@email.com')->first();
        $this->actingAs($this->user);

        $this->permissions = [
            'user',
            'role',
            'module',
        ];

        $this->roleData = [
            'name' => "Test Role",
            'permissions' => $this->permissions,
        ];

        // dd(DB::connection()->getDatabaseName());

    }

    public function test_store_creates_new_role_with_permissions()
    {
        $this->withoutExceptionHandling();

        $response = $this->post(route('role.store'), $this->roleData);

        $response->assertRedirect(route('role.index'));

        $this->assertDatabaseHas('roles', ['name' => $this->roleData['name']]);

        $role = Role::where('name', $this->roleData['name'])->first();
        $this->assertNotNull($role);

        $this->assertEquals($role->permissions->pluck('id')->toArray(), $role->permissions->pluck('id')->toArray());
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('role.store'), []);

        $response->assertSessionHasErrors(['name', 'permissions']);
    }

    public function test_store_validates_unique_role_name()
    {

        $response = $this->post(route('role.store'), [
            'name' => "Admin",
            'permissions' => ['role', 'user', 'module'],
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_edit_returns_view_for_authorized_user()
    {
        $response = $this->get(route('role.edit', 1));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.role.edit_role');
        $response->assertViewHas('data');
    }

    public function test_edit_redirects_unauthorized_user()
    {
        $this->user->syncRoles('Manager');

        $response = $this->get(route('role.edit', 1));

        $response->assertRedirect();
    }

}
