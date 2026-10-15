<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles and Permissions
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // 2. Ensure Super Admin Account
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // 3. Ensure Staff Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin2@admin.com'],
            [
                'name' => 'Admin Staff',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // 4. Seed Demo Data (Courses, Lessons, Instructors, Students, Orders, Settings)
        $this->call([
            DemoSeeder::class,
            SettingSeeder::class,
            HomeInstructorSeeder::class,
        ]);
    }
}
