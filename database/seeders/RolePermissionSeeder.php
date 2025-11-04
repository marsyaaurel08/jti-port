<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for User resource
        $userPermissions = [
            'viewAny:User',
            'view:User',
            'create:User',
            'update:User',
            'delete:User',
            'restore:User',
            'forceDelete:User',
            'forceDeleteAny:User',
            'restoreAny:User',
            'replicate:User',
            'reorder:User',
        ];

        // Create permissions for ImpersonationLog resource
        $impersonationLogPermissions = [
            'viewAny:ImpersonationLog',
            'view:ImpersonationLog',
            'create:ImpersonationLog',
            'update:ImpersonationLog',
            'delete:ImpersonationLog',
            'restore:ImpersonationLog',
            'forceDelete:ImpersonationLog',
            'forceDeleteAny:ImpersonationLog',
            'restoreAny:ImpersonationLog',
            'replicate:ImpersonationLog',
            'reorder:ImpersonationLog',
        ];

        // Create all permissions
        foreach (array_merge($userPermissions, $impersonationLogPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Super Admin gets all permissions (handled by Shield via gate)
        // No need to explicitly assign permissions to super_admin

        // Admin gets all permissions except force delete
        $adminPermissions = Permission::where('name', 'not like', 'forceDelete%')
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Editor gets view, create, and update permissions
        $editorPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'viewAny:%')
                ->orWhere('name', 'like', 'view:%')
                ->orWhere('name', 'like', 'create:%')
                ->orWhere('name', 'like', 'update:%');
        })->get();
        $editor->syncPermissions($editorPermissions);

        // User gets only view permissions
        $userPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'viewAny:%')
                ->orWhere('name', 'like', 'view:%');
        })->get();
        $user->syncPermissions($userPermissions);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
