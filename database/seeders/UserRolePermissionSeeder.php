<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function permissionGenerator($name, $permissions, $parentSerial, $parentId = null)
    {

        $childSerial = 1;

        $parent = Permission::create([
            'name' => $name,
            'parent_id' => $parentId,
            'order_serial' => $parentSerial,
            'action_menu' => 0,
            'url' => $name.'.'.'index',
        ]);

        foreach ($permissions as $permission) {
            $permission_name = $permission . ' ' . $name;

            Permission::create([
                'name' => $permission_name,
                'parent_id' => $parent->id,
                'order_serial' => $childSerial,
                'action_menu' => 1,
                'url' => $name . '.' . $permission,

            ]);

            $childSerial++;
        }
    }



    public function run()
    {

        //permission for user
        $this->permissionGenerator('general', [], 1);

        //permission for user
        $this->permissionGenerator('user', ['create', 'status', 'view', 'edit', 'delete', 'change_password'], 6, 1);

        //permission for role
        $this->permissionGenerator('role', ['create', 'edit', 'delete'], 7, 1);

        //permission for modules
        $this->permissionGenerator('module', ['create', 'edit', 'delete', 'action menu'], 8, 1);

        //permission for SiteSetting
        $this->permissionGenerator('site_setting', ['update'], 9, 1);

        // roles
        $roles = ['Software Admin', 'Admin', 'Author', 'Manager', 'contributor', "Frontend"];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }

        // add all permission in software admin role
        $role = Role::where('name', 'Software Admin')->first();
        $role->syncPermissions(Permission::all()->pluck('name'));

        // add user
        $user = User::create([
            'name' => "Rifat Hossain",
            'email' => "rifat@email.com",
            'password' => Hash::make("123456789"),
            'email_verified_at' => "2024-07-18 00:27:29"
        ]);

        $user->syncRoles('Software Admin');

        SiteSetting::create([
            'website_name' => 'Laravel Structure',
            'website_logo' => '',
        ]);
    }
}
