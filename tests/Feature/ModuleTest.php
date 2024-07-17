<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->user = User::where('email', 'rifat@email.com')->first();
        $this->actingAs($this->user);

        // dd(DB::connection()->getDatabaseName());

    }


    public function test_redirects_if_user_cannot_create_module()
    {
        $this->user->syncRoles('contributor');

        $response = $this->post(route('module.store'), [
            'name' => "test permission",
        ]);

        $response->assertRedirect(route('home'));
    }


    public function test_validates_request_data()
    {
        $response = $this->post(route('module.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }


    public function test_user_can_create_module(): void
    {

        $permission = [
            'name' => "test permission",
            'parent_id' => 999,
            'order_no' => 124,
            'action_menu' => 0,
            'url' => "test.permission.index",
        ];

        $response = $this->post(route('module.store'), $permission);

        $response->assertStatus(302);
        $this->assertEquals(124, Permission::where('url', 'test.permission.index')->first()->order_serial);

    }

    public function test_generates_order_serial_if_not_provided()
    {

        $response = $this->post(route('module.store'), [
            'name' => 'Test Permission',
            'parent_id' => null,
            'order_no' => null,
            'action_menu' => false,
            'url' => 'test-url',
            'icon' => 'test-icon',
            'icon_color' => 'test-color',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'Test Permission',
            'parent_id' => null,
            'order_serial' => 2,
        ]);
    }


    public function test_shows_edit_view_if_user_can_edit_module()
    {
        // Attempt to access the edit method
        $response = $this->get(route('module.edit',1));

        // Assert the response is ok
        $response->assertOk();

        // Assert the view is correct
        $response->assertViewIs('dashboard.module.edit_module');

    }

    public function test_user_without_permission_cannot_update_module()
    {
        $this->user->syncRoles('contributor');

        // Create a Request mock
        $response = $this->put(route('module.update', 1), [
            'name' => "test permission 2",
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('home'));

    }

    public function test_user_with_permission_can_update_module()
    {

        $response = $this->put(route('module.update', 1), [
            'name' => 'Updated Module',
            'parent_id' => null,
            'order_no' => 1,
            'action_menu' => true,
            'url' => '/updated-url',
            'icon' => 'updated-icon',
            'icon_color' => '#000000',
        ]);

        $response->assertRedirect(route('module.index'));
        $this->assertDatabaseHas('permissions', [
            'id' => 1,
            'name' => 'Updated Module',
        ]);
    }

    public function test_validates_unique_name()
    {

        $permission = [
            'name' => "test permission",
            'parent_id' => 999,
            'order_no' => 124,
            'action_menu' => 0,
            'url' => "test.permission.index",
        ];

        $this->post(route('module.store'), $permission);

        $response = $this->post(route('module.store'), $permission);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_is_required()
    {

        $response = $this->put(route('module.update', 1), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_without_permission_cannot_delete_module()
    {
        $this->user->syncRoles('contributor');
        
        $response = $this->delete(route('module.destroy', 1));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('permissions', ['id' => 1]);
    }

    public function test_user_with_permission_can_delete_module()
    {

        $response = $this->delete(route('module.destroy', 1));

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', ['id' => 1]);
    }

    public function test_deleting_non_existent_module_returns_404()
    {
        
        $nonExistentId = 9999;

        $response = $this->delete(route('module.destroy', $nonExistentId));

        $response->assertStatus(404);
    }

}
