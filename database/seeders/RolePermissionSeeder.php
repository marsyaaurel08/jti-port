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

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $pengajar = Role::firstOrCreate(['name' => 'pengajar', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Super Admin gets all permissions
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);

        // Admin gets all permissions except force delete
        $adminPermissions = Permission::where('name', 'not like', '%forceDelete%')
            ->where('name', 'not like', '%ForceDelete%')
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Pengajar gets ClassResult and MyProfilePage permissions
        $pengajarPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%ClassResult%')
                ->orWhere('name', 'View:MyProfilePage');
        })->where('name', 'not like', '%forceDelete%')
          ->where('name', 'not like', '%ForceDelete%')
          ->where('name', 'not like', '%delete%')
          ->where('name', 'not like', '%Delete%')
          ->get();
        $pengajar->syncPermissions($pengajarPermissions);

        // User role gets empty staff permissions (students only access public and student dashboard)
        $user->syncPermissions([]);

        if ($this->command) {
            $this->command->info('Roles and permissions seeded successfully!');
        }
    }
}
