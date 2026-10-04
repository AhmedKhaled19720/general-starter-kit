<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['users.manage', 'roles.manage', 'activity.view', 'project.manage'];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        Role::findOrCreate('super-admin', 'web')->syncPermissions($permissions);
        Role::findOrCreate('admin', 'web')->syncPermissions(['users.manage', 'roles.manage', 'activity.view']);
        Role::findOrCreate('user', 'web');
    }
}
