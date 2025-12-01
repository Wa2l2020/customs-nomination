<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Nomination;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FakeDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Dummy Departments
        $centralDepts = Department::factory()->count(3)->create(['parent_id' => null]);
        
        foreach ($centralDepts as $central) {
            Department::factory()->count(2)->create(['parent_id' => $central->id]);
        }

        // 2. Create Dummy Users
        // Central Admins
        foreach ($centralDepts as $central) {
            User::factory()->create([
                'role' => 'central',
                'department_id' => $central->id,
                'password' => Hash::make('password'),
            ]);
        }

        // General Managers
        $generalDepts = Department::whereNotNull('parent_id')->get();
        foreach ($generalDepts as $general) {
            User::factory()->create([
                'role' => 'general',
                'department_id' => $general->id,
                'password' => Hash::make('password'),
            ]);
        }

        // Committee Members
        User::factory()->count(3)->create([
            'role' => 'committee',
            'password' => Hash::make('password'),
        ]);

        // 3. Create Dummy Nominations with Attachments
        $categories = Category::pluck('name')->toArray();
        if (empty($categories)) {
            $this->call(QuestionsSeeder::class);
            $categories = Category::pluck('name')->toArray();
        }

        // Create a dummy file for attachments
        if (!Storage::disk('public')->exists('dummy.pdf')) {
            Storage::disk('public')->put('dummy.pdf', 'This is a dummy PDF content.');
        }

        for ($i = 0; $i < 20; $i++) {
            $general = $generalDepts->random();
            $central = $general->parent;
            $jobNumber = '100' . str_pad($i, 3, '0', STR_PAD_LEFT);

            // Create folder for this nomination
            $folder = 'nominations/' . $jobNumber;
            Storage::disk('public')->makeDirectory($folder);
            
            // Copy dummy file to nomination folder
            $fileName = $jobNumber . '_dummy_attachment.pdf';
            Storage::disk('public')->copy('dummy.pdf', $folder . '/' . $fileName);

            Nomination::create([
                'employee_name' => 'موظف تجريبي ' . ($i + 1),
                'job_number' => $jobNumber,
                'job_title' => 'مسمى وظيفي ' . ($i + 1),
                'phone' => '010000000' . $i,
                'email' => 'employee' . $i . '@customs.gov.eg',
                'central_dept_id' => $central->id,
                'general_dept_id' => $general->id,
                'department_name' => $general->name,
                'category' => $categories[array_rand($categories)],
                'job_grade' => 'اولي',
                'highest_degree' => 'بكالوريوس',
                'status' => ['pending', 'approved_general', 'approved_central', 'winner'][rand(0, 3)],
                'answers' => ['q1' => 'إجابة تجريبية 1', 'q2' => 'إجابة تجريبية 2'],
                'attachments' => [
                    ['type' => 'job_status', 'path' => $folder . '/' . $fileName],
                    ['type' => 'other', 'path' => $folder . '/' . $fileName]
                ],
            ]);
        }
    }
}
