<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Data Dummy Kategori
        $categories = [
            (object)['id' => 1, 'name' => 'Web Development', 'slug' => 'web-dev'],
            (object)['id' => 2, 'name' => 'Mobile Development', 'slug' => 'mobile-dev'],
            (object)['id' => 3, 'name' => 'Data Science', 'slug' => 'data-science'],
            (object)['id' => 4, 'name' => 'Cloud Computing', 'slug' => 'cloud-computing'],
        ];

        // Data Dummy Kursus (Meniru tabel 'courses' dan relasi 'users')
        $popularCourses = [
            (object)[
                'id' => 1,
                'title' => 'Mastering Laravel 11 for Beginners',
                'slug' => 'mastering-laravel-11',
                'instructor_name' => 'Alex Handerson', // Sesuai relasi instructor_id di ERD
                'avg_rating' => 4.8,
                'reviews_count' => 120,
                'price' => 250000,
                'level' => 'intermediate',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=500',
            ],
            (object)[
                'id' => 2,
                'title' => 'Flutter & Dart: Build Real World Apps',
                'slug' => 'flutter-dart-pro',
                'instructor_name' => 'Budi Santoso',
                'avg_rating' => 4.9,
                'reviews_count' => 85,
                'price' => 300000,
                'level' => 'beginner',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?q=80&w=500',
            ],
            (object)[
                'id' => 3,
                'title' => 'Sertifikasi Certiport: ITS Python',
                'slug' => 'its-python-cert',
                'instructor_name' => 'Sarah Wijaya',
                'avg_rating' => 4.7,
                'reviews_count' => 210,
                'price' => 0, // Free course scenario
                'level' => 'advanced',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=500',
            ],
            (object)[
                'id' => 4,
                'title' => 'UI/UX Design with Figma',
                'slug' => 'uiux-figma',
                'instructor_name' => 'Dimas Pratama',
                'avg_rating' => 4.5,
                'reviews_count' => 56,
                'price' => 150000,
                'level' => 'beginner',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?q=80&w=500',
            ],
        ];

        return view('pages.home', compact('categories', 'popularCourses'));
    }
}
