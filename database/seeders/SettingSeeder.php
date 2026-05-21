<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [

            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'general',
                'key' => 'app_logo',
                'value' => 'settings/general/logo.png',
                'type' => 'image',
            ],
            [
                'group' => 'general',
                'key' => 'app_favicon',
                'value' => 'settings/general/favicon.png',
                'type' => 'image',
            ],

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'payment',
                'key' => 'payment_gateway',
                'value' => 'midtrans',
                'type' => 'select',
            ],

            /*
            |--------------------------------------------------------------------------
            | HERO
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'hero',
                'key' => 'hero_badge_text',
                'value' => 'Official Training Center',
                'type' => 'text',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_title_line_1',
                'value' => 'Transform Your Future',
                'type' => 'text',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_title_highlight',
                'value' => 'at Jtiport',
                'type' => 'text',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_description',
                'value' => 'Akses materi eksklusif dari instruktur Polinema untuk persiapan karir global Anda. Platform Jtiport kini hadir sebagai solusi sertifikasi terpadu.',
                'type' => 'textarea',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_button_text',
                'value' => 'Mulai Belajar',
                'type' => 'text',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_button_link',
                'value' => '#course-list',
                'type' => 'text',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_image',
                'value' => 'settings/hero/hero.png',
                'type' => 'image',
            ],
            [
                'group' => 'hero',
                'key' => 'hero_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | INTRODUCTION
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'introduction',
                'key' => 'introduction_badge_text',
                'value' => 'About Jtiport',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_title_line_1',
                'value' => 'Platform Pelatihan &',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_title_highlight',
                'value' => 'Ujian Sertifikasi',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_quote',
                'value' => 'Memastikan setiap mahasiswa memiliki standar kompetensi global melalui ekosistem digital yang terintegrasi.',
                'type' => 'textarea',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_description',
                'value' => 'Sebagai Certiport Authorized Testing Center (CATC), Jurusan Teknologi Informasi menyediakan platform Jtiport untuk mendukung program pelatihan yang efisien. Kami menjembatani kebutuhan industri dengan kurikulum praktis yang dapat diakses kapan saja.',
                'type' => 'textarea',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_main_image',
                'value' => 'settings/introduction/main-image.png',
                'type' => 'image',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_overlay_image',
                'value' => 'settings/introduction/overlay-image.png',
                'type' => 'image',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_floating_title',
                'value' => 'CATC',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_floating_subtitle',
                'value' => 'Authorized Center',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_1_icon',
                'value' => 'check-circle-2',
                'type' => 'icon',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_1_title',
                'value' => 'Kurikulum CATC',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_1_description',
                'value' => 'Materi standar internasional dari vendor resmi.',
                'type' => 'textarea',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_2_icon',
                'value' => 'layout',
                'type' => 'icon',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_2_title',
                'value' => 'Jtiport Hub',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_feature_2_description',
                'value' => 'Satu pintu untuk semua data pelatihan Anda.',
                'type' => 'textarea',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_button_text',
                'value' => 'Tentang CATC',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_button_link',
                'value' => 'https://certiport.pearsonvue.com/CATCs.aspx',
                'type' => 'text',
            ],
            [
                'group' => 'introduction',
                'key' => 'introduction_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | BENEFIT
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'benefit',
                'key' => 'benefit_title_line_1',
                'value' => 'The Reasons',
                'type' => 'text',
            ],
            [
                'group' => 'benefit',
                'key' => 'benefit_title_line_2',
                'value' => 'to Choose',
                'type' => 'text',
            ],
            [
                'group' => 'benefit',
                'key' => 'benefit_title_highlight',
                'value' => 'Jtiport',
                'type' => 'text',
            ],
            [
                'group' => 'benefit',
                'key' => 'benefit_description',
                'value' => 'Kami berdedikasi untuk memberikan pengalaman belajar terbaik dengan fasilitas modern dan jaringan industri yang luas bagi seluruh peserta.',
                'type' => 'textarea',
            ],
            [
                'group' => 'benefit',
                'key' => 'benefit_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | INSTRUCTORS
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'instructors',
                'key' => 'instructors_title_line_1',
                'value' => 'Learn from',
                'type' => 'text',
            ],
            [
                'group' => 'instructors',
                'key' => 'instructors_title_highlight',
                'value' => 'Experienced Instructor',
                'type' => 'text',
            ],
            [
                'group' => 'instructors',
                'key' => 'instructors_description',
                'value' => 'Kelas di Jtiport dibina langsung oleh para pengajar ahli dari Jurusan Teknologi Informasi Polinema yang memiliki sertifikasi industri internasional.',
                'type' => 'textarea',
            ],
            [
                'group' => 'instructors',
                'key' => 'instructors_button_text',
                'value' => 'Lihat Kelas Kami',
                'type' => 'text',
            ],
            [
                'group' => 'instructors',
                'key' => 'instructors_button_link',
                'value' => '#classes-list',
                'type' => 'text',
            ],
            [
                'group' => 'instructors',
                'key' => 'instructors_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | CTA
            |--------------------------------------------------------------------------
            */
            [
                'group' => 'cta',
                'key' => 'cta_badge_text',
                'value' => 'Official JTIPORT',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_title_line_1',
                'value' => 'Your Future',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_title_line_2',
                'value' => 'Starts',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_title_highlight',
                'value' => 'Here.',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_rotating_text',
                'value' => '• Jtiport Certification • Join Now •',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_heading',
                'value' => 'Siap untuk memulai pelatihan pertama Anda?',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_description',
                'value' => 'Jangan biarkan peluang karir global terlewatkan. Bergabunglah dengan ribuan mahasiswa lainnya di platform Jtiport dan akses materi persiapan ujian terbaik dari instruktur ahli Polinema.',
                'type' => 'textarea',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_primary_button_text',
                'value' => 'Daftar Akun Baru',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_primary_button_link',
                'value' => '/register',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_secondary_button_text',
                'value' => 'Konsultasi Program',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_whatsapp_number',
                'value' => '628123456789',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_footer_text',
                'value' => 'Bergabung dengan',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_footer_highlight',
                'value' => '2,000+',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_footer_suffix',
                'value' => 'mahasiswa bersertifikasi.',
                'type' => 'text',
            ],
            [
                'group' => 'cta',
                'key' => 'cta_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                [
                    'key' => $setting['key'],
                ],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
