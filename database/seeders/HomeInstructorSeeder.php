<?php

namespace Database\Seeders;

use App\Models\HomeInstructor;
use Illuminate\Database\Seeder;

class HomeInstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructors = [
            [
                'name' => 'Dr. Eng. Rosa',
                'role' => 'Web Expert',
                'image' => 'settings/instructors/instructor-1.png',
                'bio' => 'Instruktur berpengalaman dalam pengembangan web modern dan teknologi frontend.',
                'linkedin' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],

            [
                'name' => 'M. Fachruddin, M.T',
                'role' => 'Data Science',
                'image' => 'settings/instructors/instructor-2.png',
                'bio' => 'Berfokus pada machine learning, artificial intelligence, dan data analytics.',
                'linkedin' => null,
                'sort_order' => 2,
                'is_active' => true,
            ],

            [
                'name' => 'Indra Ridwan, Ph.D',
                'role' => 'Cloud Architect',
                'image' => 'settings/instructors/instructor-3.png',
                'bio' => 'Memiliki pengalaman dalam cloud infrastructure dan distributed systems.',
                'linkedin' => null,
                'sort_order' => 3,
                'is_active' => true,
            ],

            [
                'name' => 'Siska Juwita, M.Kom',
                'role' => 'UI/UX Expert',
                'image' => 'settings/instructors/instructor-4.png',
                'bio' => 'Spesialis pada desain antarmuka modern dan user experience.',
                'linkedin' => null,
                'sort_order' => 4,
                'is_active' => true,
            ],

            [
                'name' => 'Budi Raharjo, ST',
                'role' => 'Cyber Security',
                'image' => 'settings/instructors/instructor-5.png',
                'bio' => 'Fokus pada keamanan jaringan, penetration testing, dan digital security.',
                'linkedin' => null,
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($instructors as $instructor) {
            HomeInstructor::updateOrCreate(
                [
                    'name' => $instructor['name'],
                ],
                $instructor
            );
        }
    }
}
