<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Admin Users
        $admins = [
            [
                'name' => 'مدير النظام (Wa2l)',
                'email' => 'Wa2l',
                'password' => '3416wa2l',
            ],
            [
                'name' => 'مدير النظام (Wa2latia)',
                'email' => 'wa2latia@gmail.com',
                'password' => '3416Wa@',
            ]
        ];

        foreach ($admins as $admin) {
            if (!User::where('email', $admin['email'])->exists()) {
                User::create([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                    'plain_password' => $admin['password'],
                    'role' => 'admin',
                    'job_number' => '00000',
                    'phone' => '0000000000',
                ]);
            }
        }

        // 2. Default Settings
        $defaults = [
            'site_title' => 'نظام الترشيحات والتكريم',
            'logo_url' => 'https://customs.gov.eg/images/EcaLogoLarge.png',
            'theme_color' => '#003366',
            'max_categories_central' => 5,
            'max_categories_general' => 3,
            'support_url' => '#',
            'terms_text' => 'أقر أنا المرشح بصحة جميع البيانات الواردة في هذه الاستمارة، وفي حالة ثبوت عدم صحة أي منها أتحمل كافة المسئولية القانونية والإدارية.',
            'committee_registration_password' => '1232',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // 3. Seed Questions
        $this->call(QuestionsSeeder::class);
    }
}
