<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
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

    public function test_store_creates_new_user_for_authorized_user()
    {
        $role = Role::findOrFail(1);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [$role->id],
        ];

        $response = $this->post(route('user.store'), $userData);

        $response->assertRedirect(route('user.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue($user->hasRole($role->name));
    }

    public function test_store_redirects_unauthorized_user()
    {
        $this->user->syncRoles('contributor');

        $response = $this->post(route('user.store'), []);

        $response->assertStatus(403);

        // $response->assertRedirect(route('home'));
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('user.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'roles', 'password']);
    }

    public function test_store_validates_unique_name_and_email()
    {
        $existingUser = User::findOrFail(1);
        $role = Role::findOrFail(1);

        $userData = [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [$role->name],
        ];

        $response = $this->post(route('user.store'), $userData);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_store_validates_password_confirmation()
    {

        $role = Role::findOrFail(1);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'roles' => [$role->id],
        ];

        $response = $this->post(route('user.store'), $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_store_validates_password_length()
    {
        $role = Role::findOrFail(1);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'roles' => [$role->name],
        ];

        $response = $this->post(route('user.store'), $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_show_method_returns_correct_view_with_user_data()
    {
        $response = $this->get(route('user.show', $this->user));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.user.profile');
        $response->assertViewHas('data');

        $viewData = $response->viewData('data');
        $this->assertIsObject($viewData);
        $this->assertInstanceOf(User::class, $viewData->user);
        $this->assertEquals($this->user->id, $viewData->user->id);
    }

    public function test_edit_method_redirects_unauthorized_user()
    {

        $this->user->syncRoles('contributor');

        $response = $this->get(route('user.edit', $this->user));

        $response->assertRedirect();
    }

    public function test_edit_method_returns_correct_view_for_authorized_user()
    {

        $this->user->syncRoles('Software Admin');

        $response = $this->get(route('user.edit', $this->user));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.user.edit_user');
        $response->assertViewHas('data');

        $viewData = $response->viewData('data');
        $this->assertIsObject($viewData);
        $this->assertInstanceOf(User::class, $viewData->user);
        $this->assertEquals($this->user->id, $viewData->user->id);
        // $this->assertIsIterable($viewData->roles);
    }

    public function test_update_method_redirects_unauthorized_user()
    {

        $this->user->syncRoles('contributor');

        $response = $this->put(route('user.update', $this->user->id), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'roles' => ['contributor']
        ]);

        $response->assertRedirect();
    }

    public function test_update_method_validates_input()
    {

        $response = $this->put(route('user.update', $this->user->id), [
            'name' => '',
            'email' => 'not-an-email',
            'roles' => []
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);
    }

    public function test_update_method_updates_user_and_syncs_roles()
    {

        $response = $this->put(route('user.update', $this->user->id), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'roles' => ['contributor', 'Admin']
        ]);

        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('message', 'User Successfully Updated');

        $this->user->refresh();

        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updated@example.com', $this->user->email);
        $this->assertTrue($this->user->hasRole('contributor'));
        $this->assertTrue($this->user->hasRole('Admin'));
    }

    public function test_update_method_prevents_duplicate_email()
    {

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->put(route('user.update', $this->user->id), [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
            'roles' => ['contributor']
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_destroy_method_redirects_unauthorized_user()
    {

        $this->user->syncRoles('contributor');

        $response = $this->delete(route('user.destroy', $this->user));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    public function test_destroy_method_deletes_user_for_authorized_user()
    {

        $response = $this->delete(route('user.destroy', $this->user));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'User Deleted Successfully');
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_destroy_method_returns_to_previous_page()
    {

        $response = $this->from('/previous-page')->delete(route('user.destroy', $this->user));

        $response->assertRedirect('/previous-page');
    }

    public function test_change_pass_method_redirects_unauthorized_user()
    {

        Role::create([
            'name' => 'Test Role'
        ]);

        $this->user->syncRoles('Test Role');

        $response = $this->post(route('changepassword', $this->user), [
            'old_password' => 'old_password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionMissing('message', 'Password Updated Successfully');
    }

    public function test_change_pass_method_validates_input()
    {

        $response = $this->post(route('changepassword', $this->user), [
            'old_password' => '',
            'password' => 'new_password',
            'password_confirmation' => 'different_password'
        ]);

        $response->assertSessionHasErrors(['old_password', 'password']);
    }

    public function test_change_pass_method_changes_password_with_correct_old_password()
    {

        $response = $this->post(route('changepassword', $this->user), [
            'old_password' => "123456789",
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('message', 'Password Updated Successfully');
        $this->assertTrue(Hash::check('new_password', $this->user->refresh()->password));
    }

    public function test_change_pass_method_does_not_change_password_with_incorrect_old_password()
    {

        $response = $this->post(route('changepassword', $this->user), [
            'old_password' => 'wrong_old_password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ]);

        $response->assertRedirect(route('home'));
        $this->assertFalse(Hash::check("new_password", $this->user->password));
    }
}
