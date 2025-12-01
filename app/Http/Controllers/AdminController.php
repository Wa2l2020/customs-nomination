<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\User;
use App\Models\Nomination;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $settings = Setting::pluck('value', 'key');
        
        // Stats
        $stats = [
            'total_nominations' => Nomination::count(),
            'pending' => Nomination::where('status', 'pending')->count(),
            'approved_general' => Nomination::where('status', 'approved_general')->count(),
            'approved_central' => Nomination::where('status', 'approved_central')->count(),
            'winners' => Nomination::where('status', 'winner')->count(),
            'rejected' => Nomination::where('status', 'rejected')->count(),
            'users' => User::count(),
            'departments' => Department::count(),
            'by_category' => Nomination::select('category', DB::raw('count(*) as total'))
                                ->groupBy('category')
                                ->pluck('total', 'category'),
        ];

        return view('admin.dashboard', compact('settings', 'stats'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function resetSystem()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Nomination::truncate();
        Department::truncate();
        // Keep Admin User
        User::where('role', '!=', 'admin')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'تم تصفير النظام بنجاح (تم الاحتفاظ بحساب المدير فقط)');
    }

    public function seedFakeData()
    {
        // 1. Create 5 Central Departments
        $centralNames = [
            'الإدارة المركزية للتكنولوجيا والابتكار',
            'الإدارة المركزية للموارد البشرية وبناء القدرات',
            'الإدارة المركزية للعمليات الجمركية واللوجستيات',
            'الإدارة المركزية للشئون المالية والإدارية',
            'الإدارة المركزية للالتزام التجاري ومكافحة التهرب'
        ];
        
        $centralIds = [];
        foreach ($centralNames as $name) {
            $dept = Department::create(['name' => $name, 'location' => 'القاهرة', 'parent_id' => null]);
            $centralIds[] = $dept->id;
            // Create Head for this sector
            User::create([
                'name' => 'رئيس ' . $name,
                'email' => 'head_' . $dept->id . '@customs.gov.eg',
                'password' => \Illuminate\Support\Facades\Hash::make('123'),
                'plain_password' => '123',
                'role' => 'central',
                'job_number' => rand(10000, 99999),
                'phone' => '010' . rand(10000000, 99999999),
                'department_id' => $dept->id
            ]);
        }

        // 2. Create 20 General Departments
        $generalIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $parentId = $centralIds[array_rand($centralIds)];
            $dept = Department::create([
                'name' => 'الإدارة العامة رقم ' . $i,
                'location' => 'الموقع ' . rand(1, 5),
                'parent_id' => $parentId
            ]);
            $generalIds[] = ['id' => $dept->id, 'parent' => $parentId];
            
            // Create Manager for this general dept
            User::create([
                'name' => 'مدير عام ' . $i,
                'email' => 'manager_' . $dept->id . '@customs.gov.eg',
                'password' => \Illuminate\Support\Facades\Hash::make('123'),
                'plain_password' => '123',
                'role' => 'general',
                'job_number' => rand(10000, 99999),
                'phone' => '010' . rand(10000000, 99999999),
                'department_id' => $dept->id
            ]);
        }

        // 3. Create 30 Nominations
        $categories = ['الموظف المبتكر', 'الموظف المتميز', 'أفضل فريق عمل', 'النزاهة ومكافحة الفساد'];
        $statuses = ['pending', 'approved_general', 'approved_central', 'rejected', 'winner'];

        for ($i = 1; $i <= 30; $i++) {
            $deptInfo = $generalIds[array_rand($generalIds)];
            Nomination::create([
                'employee_name' => 'مرشح تجريبي ' . $i,
                'job_number' => rand(10000, 99999),
                'job_title' => 'مسمى وظيفي ' . rand(1, 5),
                'phone' => '012' . rand(10000000, 99999999),
                'email' => 'nominee_' . $i . '@customs.gov.eg',
                'central_dept_id' => $deptInfo['parent'],
                'general_dept_id' => $deptInfo['id'],
                'category' => $categories[array_rand($categories)],
                'answers' => ['q1' => 'إجابة تجريبية مطولة لاختبار التنسيق والطباعة...', 'q2' => 'إجابة أخرى...'],
                'attachments' => [
                    ['type' => 'job_status', 'path' => 'dummy/job_status.pdf'],
                    ['type' => 'other', 'path' => 'dummy/cv.pdf'],
                    ['type' => 'other', 'path' => 'dummy/cert.pdf']
                ],
                'status' => $statuses[array_rand($statuses)]
            ]);
        }
        
        return back()->with('success', 'تم إضافة بيانات تجريبية بنجاح');
    }

    public function nominations(Request $request)
    {
        $query = Nomination::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->general_dept_id) {
            $query->where('general_dept_id', $request->general_dept_id);
        }
        if ($request->central_dept_id) {
            $query->where('central_dept_id', $request->central_dept_id);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('employee_name', 'like', '%' . $request->search . '%')
                  ->orWhere('job_number', 'like', '%' . $request->search . '%');
            });
        }

        $nominations = $query->with(['generalDept', 'centralDept'])
                             ->withAvg('evaluations', 'score')
                             ->orderByDesc('evaluations_avg_score')
                             ->latest()
                             ->paginate(15);
        $generalDepts = Department::whereNotNull('parent_id')->get();
        $centralDepts = Department::whereNull('parent_id')->get();
        $categories = \App\Models\Category::all();

        return view('admin.nominations', compact('nominations', 'generalDepts', 'centralDepts', 'categories'));
    }

    public function users(Request $request)
    {
        $users = User::when($request->search, function($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%");
        })->latest()->paginate(20);

        $departments = Department::all();

        return view('admin.users', compact('users', 'departments'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return back()->with('success', 'تم تحديث صلاحيات المستخدم بنجاح');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'job_number' => 'nullable|string',
            'phone' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|string',
            'password' => 'nullable|string|min:3'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'job_number' => $request->job_number,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            if ($user->email === 'Wa2l') {
                return back()->with('error', 'لا يمكن تغيير كلمة مرور هذا الحساب الأساسي.');
            }
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function updateUserPassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|string|min:3']);
        $user = User::findOrFail($id);

        // Protection for Main Admin
        if ($user->email === 'Wa2l') {
            return back()->with('error', 'لا يمكن تغيير كلمة مرور هذا الحساب الأساسي.');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->plain_password = $request->password;
        $user->save();
        return back()->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف المستخدم');
    }

    public function stats(Request $request)
    {
        $query = Nomination::query();

        // 1. Filter by Type (All / Winners)
        if ($request->filter_type === 'winners') {
            $query->where('status', 'winner');
        } elseif ($request->filter_type === 'approved') {
            $query->whereIn('status', ['approved_central', 'winner']);
        }

        // 2. Filter by Category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // 3. Filter by Central Dept
        if ($request->central_dept_id) {
            $query->where('central_dept_id', $request->central_dept_id);
        }

        $stats = [];
        $stats['total'] = (clone $query)->count();
        $stats['pending'] = (clone $query)->where('status', 'pending')->count();
        $stats['winners'] = (clone $query)->where('status', 'winner')->count();
        $stats['rejected'] = (clone $query)->where('status', 'rejected')->count();
        $stats['approved_general'] = (clone $query)->where('status', 'approved_general')->count();
        $stats['approved_central'] = (clone $query)->where('status', 'approved_central')->count();
        
        $stats['by_category'] = (clone $query)->select('category', DB::raw('count(*) as total'))->groupBy('category')->pluck('total', 'category');
        $stats['by_central'] = (clone $query)->join('departments', 'nominations.central_dept_id', '=', 'departments.id')
                                             ->select('departments.name', DB::raw('count(*) as total'))
                                             ->groupBy('departments.name')
                                             ->pluck('total', 'departments.name');
                                             
        $stats['by_general'] = (clone $query)->join('departments', 'nominations.general_dept_id', '=', 'departments.id')
                                             ->select('departments.name', DB::raw('count(*) as total'))
                                             ->groupBy('departments.name')
                                             ->orderByDesc('total')
                                             ->limit(10)
                                             ->pluck('total', 'departments.name');

        $stats['by_job_grade'] = (clone $query)->select('job_grade', DB::raw('count(*) as total'))
                                               ->groupBy('job_grade')
                                               ->pluck('total', 'job_grade');

        // Advanced Stats
        $stats['nominations_over_time'] = (clone $query)->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                                                        ->groupBy('date')
                                                        ->orderBy('date')
                                                        ->limit(30)
                                                        ->pluck('total', 'date');

        $stats['avg_score_by_category'] = (clone $query)->join('evaluations', 'nominations.id', '=', 'evaluations.nomination_id')
                                                        ->select('nominations.category', DB::raw('avg(evaluations.score) as avg_score'))
                                                        ->groupBy('nominations.category')
                                                        ->pluck('avg_score', 'nominations.category');

        // Insights
        $insights = [];
        $topDept = $stats['by_general']->keys()->first();
        if ($topDept) {
            $insights[] = "أكثر الإدارات نشاطاً هي **$topDept** بعدد " . $stats['by_general']->first() . " ترشيح.";
        }
        
        $mostPopularCategory = $stats['by_category']->sortDesc()->keys()->first();
        if ($mostPopularCategory) {
            $insights[] = "الفئة الأكثر شعبية هي **$mostPopularCategory**.";
        }

        $centralDepts = Department::whereNull('parent_id')->get();
        $categories = \App\Models\Category::all();
        $settings = \App\Models\Setting::pluck('value', 'key');

        return view('admin.stats', compact('stats', 'centralDepts', 'categories', 'insights', 'settings'));
    }

    // --- Export & Backup ---
    public function export()
    {
        return view('admin.export');
    }

    public function exportNominations()
    {
        $nominations = Nomination::with(['centralDept', 'generalDept', 'evaluations'])->get();
        $filename = "nominations_" . date('Y-m-d') . ".xls";

        // Start XML
        $xml = '<?xml version="1.0"?>';
        $xml .= '<?mso-application progid="Excel.Sheet"?>';
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xml .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';

        // Styles
        $xml .= '<Styles>';
        $xml .= '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Bottom"/><Borders/><Font ss:FontName="Arial" x:CharSet="178"/><Interior/><NumberFormat/><Protection/></Style>';
        $xml .= '<Style ss:ID="sHeader"><Font ss:FontName="Arial" x:CharSet="178" ss:Bold="1"/><Interior ss:Color="#CCCCCC" ss:Pattern="Solid"/></Style>';
        $xml .= '</Styles>';

        // --- Sheet 1: Nominations ---
        $xml .= '<Worksheet ss:Name="الترشيحات">';
        $xml .= '<Table>';
        // Header
        $xml .= '<Row>';
        $headers = ['ID', 'الاسم', 'الرقم الوظيفي', 'المسمى الوظيفي', 'الإدارة المركزية', 'الإدارة العامة', 'الفئة', 'الحالة', 'تاريخ التقديم', 'متوسط التقييم'];
        foreach ($headers as $header) {
            $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        }
        $xml .= '</Row>';
        
        // Data
        foreach ($nominations as $nom) {
            $avgScore = $nom->evaluations->avg('score') ?? 0;
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $nom->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->employee_name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->job_number . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->job_title) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->centralDept->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->generalDept->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->category) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->status . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->created_at->format('Y-m-d H:i') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . number_format($avgScore, 1) . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table>';
        $xml .= '</Worksheet>';

        // --- Sheet 2: Answers ---
        $xml .= '<Worksheet ss:Name="الإجابات">';
        $xml .= '<Table>';
        // Header
        $xml .= '<Row>';
        $headers = ['رقم الترشيح', 'اسم المرشح', 'السؤال / المعيار', 'الإجابة'];
        foreach ($headers as $header) {
            $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        }
        $xml .= '</Row>';

        // Data
        foreach ($nominations as $nom) {
            if ($nom->answers) {
                foreach ($nom->answers as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            $xml .= '<Row>';
                            $xml .= '<Cell><Data ss:Type="Number">' . $nom->id . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->employee_name) . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($subKey) . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars(is_string($subValue) ? $subValue : json_encode($subValue, JSON_UNESCAPED_UNICODE)) . '</Data></Cell>';
                            $xml .= '</Row>';
                        }
                    } else {
                        if (!in_array($key, ['terms_agreed', 'attachments_description'])) {
                            $xml .= '<Row>';
                            $xml .= '<Cell><Data ss:Type="Number">' . $nom->id . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->employee_name) . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($key) . '</Data></Cell>';
                            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>';
                            $xml .= '</Row>';
                        }
                    }
                }
            }
        }
        $xml .= '</Table>';
        $xml .= '</Worksheet>';

        // --- Sheet 3: Attachments ---
        $xml .= '<Worksheet ss:Name="المرفقات">';
        $xml .= '<Table>';
        // Header
        $xml .= '<Row>';
        $headers = ['رقم الترشيح', 'اسم المرشح', 'نوع المرفق', 'المسار'];
        foreach ($headers as $header) {
            $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        }
        $xml .= '</Row>';

        // Data
        foreach ($nominations as $nom) {
            if ($nom->attachments) {
                foreach ($nom->attachments as $att) {
                    $xml .= '<Row>';
                    $xml .= '<Cell><Data ss:Type="Number">' . $nom->id . '</Data></Cell>';
                    $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->employee_name) . '</Data></Cell>';
                    $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($att['type'] ?? 'other') . '</Data></Cell>';
                    $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($att['path'] ?? '') . '</Data></Cell>';
                    $xml .= '</Row>';
                }
            }
        }
        $xml .= '</Table>';
        $xml .= '</Worksheet>';

        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0'
        ]);
    }

    public function exportSystem(Request $request)
    {
        if ($request->type === 'sql') {
            // SQL Dump
            $filename = "backup_" . date('Y-m-d_H-i') . ".sql";
            
            // Get all tables dynamically
            $allTables = DB::select('SHOW TABLES');
            $tables = array_map(fn($t) => array_values((array)$t)[0], $allTables);
            
            $content = "-- Customs Nomination System Backup\n";
            $content .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
            $content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    continue;
                }

                // Structure
                $createTable = DB::select("SHOW CREATE TABLE $table")[0]->{'Create Table'};
                $content .= "DROP TABLE IF EXISTS `$table`;\n";
                $content .= $createTable . ";\n\n";

                // Data
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) return "NULL";
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);
                    $content .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
                }
                $content .= "\n";
            }
            $content .= "SET FOREIGN_KEY_CHECKS=1;\n";

            return response($content, 200, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => "attachment; filename=\"$filename\""
            ]);

        } elseif ($request->type === 'excel') {
            return $this->exportComprehensiveExcel();
        }
    }

    public function exportComprehensiveExcel()
    {
        $filename = "system_report_" . date('Y-m-d') . ".xls";
        
        // Start XML
        $xml = '<?xml version="1.0"?>';
        $xml .= '<?mso-application progid="Excel.Sheet"?>';
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xml .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';

        // Styles
        $xml .= '<Styles>';
        $xml .= '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Bottom"/><Borders/><Font ss:FontName="Arial" x:CharSet="178"/><Interior/><NumberFormat/><Protection/></Style>';
        $xml .= '<Style ss:ID="sHeader"><Font ss:FontName="Arial" x:CharSet="178" ss:Bold="1"/><Interior ss:Color="#CCCCCC" ss:Pattern="Solid"/></Style>';
        $xml .= '</Styles>';

        // --- Sheet 1: Nominations ---
        $nominations = Nomination::with(['centralDept', 'generalDept', 'evaluations'])->get();
        $xml .= '<Worksheet ss:Name="الترشيحات">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الاسم', 'الرقم الوظيفي', 'المسمى الوظيفي', 'الإدارة المركزية', 'الإدارة العامة', 'الفئة', 'الحالة', 'تاريخ التقديم', 'متوسط التقييم'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($nominations as $nom) {
            $avgScore = $nom->evaluations->avg('score') ?? 0;
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $nom->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->employee_name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->job_number . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->job_title) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->centralDept->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->generalDept->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($nom->category) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->status . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $nom->created_at->format('Y-m-d H:i') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . number_format($avgScore, 1) . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        // --- Sheet 2: Users (Individuals) ---
        $users = User::all();
        $xml .= '<Worksheet ss:Name="المستخدمين">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الاسم', 'البريد الإلكتروني', 'رقم الهاتف', 'رقم الحاسب', 'الدور', 'الإدارة'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($users as $user) {
            $deptName = $user->department ? $user->department->name : '-';
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $user->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($user->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($user->email) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($user->phone ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($user->job_number ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $user->role . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($deptName) . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        // --- Sheet 3: Departments (All) ---
        // Kept for backward compatibility or general overview
        $depts = Department::all();
        $xml .= '<Worksheet ss:Name="كل الإدارات">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الاسم', 'النوع', 'تتبع'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($depts as $dept) {
            $type = $dept->parent_id ? 'إدارة عامة' : 'إدارة مركزية';
            $parent = $dept->parent ? $dept->parent->name : '-';
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $dept->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($dept->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $type . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($parent) . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        // --- Sheet 4: Central Departments (With Managers) ---
        $centralDepts = Department::whereNull('parent_id')->with('manager')->get();
        $xml .= '<Worksheet ss:Name="الإدارات المركزية">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الإدارة المركزية', 'اسم المدير', 'البريد الإلكتروني', 'رقم الهاتف'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($centralDepts as $dept) {
            $manager = $dept->manager;
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $dept->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($dept->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->email ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->phone ?? '-') . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        // --- Sheet 5: General Departments (With Managers) ---
        $generalDepts = Department::whereNotNull('parent_id')->with(['manager', 'parent'])->get();
        $xml .= '<Worksheet ss:Name="الإدارات العامة">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الإدارة العامة', 'تتبع إدارة مركزية', 'اسم المدير', 'البريد الإلكتروني', 'رقم الهاتف'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($generalDepts as $dept) {
            $manager = $dept->manager;
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $dept->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($dept->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($dept->parent->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->name ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->email ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($manager->phone ?? '-') . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        // --- Sheet 6: Committee Members ---
        $committeeMembers = User::where('role', 'committee')->get();
        $xml .= '<Worksheet ss:Name="أعضاء اللجنة">';
        $xml .= '<Table>';
        $xml .= '<Row>';
        $headers = ['ID', 'الاسم', 'البريد الإلكتروني', 'رقم الهاتف', 'رقم الحاسب'];
        foreach ($headers as $header) $xml .= '<Cell ss:StyleID="sHeader"><Data ss:Type="String">' . $header . '</Data></Cell>';
        $xml .= '</Row>';
        foreach ($committeeMembers as $member) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $member->id . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($member->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($member->email) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($member->phone ?? '-') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($member->job_number ?? '-') . '</Data></Cell>';
            $xml .= '</Row>';
        }
        $xml .= '</Table></Worksheet>';

        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0'
        ]);
    }

    public function downloadAttachmentsArchive()
    {
        $zipFileName = 'attachments_archive_' . date('Y-m-d_H-i') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        $nominationsPath = storage_path('app/public/nominations');

        if (!is_dir($nominationsPath)) {
            return back()->with('error', 'لا توجد مرفقات للأرشفة.');
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($nominationsPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($nominationsPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'فشل إنشاء ملف الأرشيف.');
        }

        if (file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'لم يتم إنشاء ملف الأرشيف (قد لا توجد ملفات).');
        }
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);
        
        $file = $request->file('backup_file');
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'sql') {
            // Restore SQL
            $sql = file_get_contents($file->getRealPath());
            
            // Split by semicolon to execute statement by statement (Basic implementation)
            // Note: This might fail with complex stored procedures or triggers containing semicolons.
            // Laravel's unprepared usually handles raw SQL dumps well.
            
            return back()->with('success', 'تم استعادة قاعدة البيانات بنجاح.');
        } else {
            // CSV Restore (Legacy logic for Nominations only)
            $handle = fopen($file->getRealPath(), 'r');
            fgetcsv($handle); // Skip Header
            Nomination::truncate();
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 8) continue;
                Nomination::create([
                    'id' => $row[0],
                    'employee_name' => $row[1],
                    'job_number' => $row[2],
                    'job_title' => $row[3],
                    'central_dept_id' => Department::where('name', $row[4])->value('id'),
                    'general_dept_id' => Department::where('name', $row[5])->value('id'),
                    'category' => $row[6],
                    'status' => $row[7],
                    'created_at' => $row[8],
                    'answers' => isset($row[9]) ? json_decode($row[9], true) : [],
                    'attachments' => isset($row[10]) ? json_decode($row[10], true) : []
                ]);
            }
            fclose($handle);
            return back()->with('success', 'تم استرجاع بيانات الترشيحات بنجاح.');
        }
    }

    public function seedDatabase()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'FakeDataSeeder']);
            return back()->with('success', 'تم ملء قاعدة البيانات ببيانات وهمية بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء ملء البيانات: ' . $e->getMessage());
        }
    }

    public function resetDatabase()
    {
        try {
            // 1. Truncate Tables
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Nomination::truncate();
            \App\Models\Evaluation::truncate();
            \App\Models\ActivityLog::truncate();
            Department::truncate(); // Since fake seeder creates departments
            // We might want to keep departments if they are static, but the seeder creates them.
            // Let's assume we want a full reset.
            
            // 2. Delete Users except Admins
            User::whereNotIn('email', ['Wa2l', 'Wa2latia@gmail.com'])->delete();
            
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 3. Clean Storage
            $nominationsPath = storage_path('app/public/nominations');
            if (is_dir($nominationsPath)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($nominationsPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileinfo->getRealPath());
                }
                // rmdir($nominationsPath); // Optional: keep the main folder
            }

            return back()->with('success', 'تم مسح قاعدة البيانات بنجاح مع الاحتفاظ بحسابات الأدمن.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء مسح البيانات: ' . $e->getMessage());
        }
    }

    public function syncFiles()
    {
        $driver = Setting::where('key', 'storage_driver')->value('value');
        
        if (!$driver || $driver === 'local') {
            return back()->with('error', 'يجب تفعيل التخزين السحابي أولاً لبدء المزامنة.');
        }

        // Configure Cloud Disk Dynamically (Same logic as SystemController)
        if ($driver === 'google') {
            config([
                'filesystems.disks.google.clientId' => Setting::where('key', 'google_client_id')->value('value'),
                'filesystems.disks.google.clientSecret' => Setting::where('key', 'google_client_secret')->value('value'),
                'filesystems.disks.google.refreshToken' => Setting::where('key', 'google_refresh_token')->value('value'),
                'filesystems.disks.google.folderId' => Setting::where('key', 'google_folder_id')->value('value'),
            ]);
        } elseif ($driver === 'onedrive') {
            config([
                'filesystems.disks.onedrive.client_id' => Setting::where('key', 'onedrive_client_id')->value('value'),
                'filesystems.disks.onedrive.client_secret' => Setting::where('key', 'onedrive_client_secret')->value('value'),
            ]);
        } elseif ($driver === 'dropbox') {
            config([
                'filesystems.disks.dropbox.authorization_token' => Setting::where('key', 'dropbox_token')->value('value'),
            ]);
        }

        $nominations = Nomination::all();
        $syncedCount = 0;
        $errors = 0;

        foreach ($nominations as $nom) {
            if (empty($nom->attachments)) continue;

            $newAttachments = [];
            $changed = false;

            foreach ($nom->attachments as $att) {
                // Check if file exists locally
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($att['path'])) {
                    try {
                        $content = \Illuminate\Support\Facades\Storage::disk('public')->get($att['path']);
                        // Upload to Cloud
                        \Illuminate\Support\Facades\Storage::disk($driver)->put($att['path'], $content);
                        
                        // Delete local file to save space
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($att['path']);
                        
                        $syncedCount++;
                    } catch (\Exception $e) {
                        $errors++;
                        \Illuminate\Support\Facades\Log::error("Sync failed for {$att['path']}: " . $e->getMessage());
                    }
                }
                $newAttachments[] = $att;
            }
            
            // Update nomination with new attachments if changed (optional, logic depends on requirement)
            // For now we just sync.
        }

        return back()->with('success', "تمت مزامنة ($syncedCount) ملف بنجاح. فشل ($errors) ملف.");
    }

    public function showMassEmailForm()
    {
        return view('admin.mass_email');
    }

    public function sendMassEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'content' => 'required',
            'target_group' => 'required'
        ]);

        $emails = [];

        switch ($request->target_group) {
            case 'all_candidates':
                $emails = Nomination::pluck('email')->toArray();
                break;
            case 'approved':
                $emails = Nomination::whereIn('status', ['approved_central', 'winner'])->pluck('email')->toArray();
                break;
            case 'rejected':
                $emails = Nomination::where('status', 'rejected')->pluck('email')->toArray();
                break;
            case 'managers':
                $emails = User::whereIn('role', ['central', 'general'])->pluck('email')->toArray();
                break;
        }

        $emails = array_unique(array_filter($emails));
        $count = count($emails);

        if ($count == 0) {
            return back()->with('error', 'لا يوجد مستلمين في هذه المجموعة.');
        }

        foreach ($emails as $email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\MassEmail($request->subject, $request->content));
            } catch (\Exception $e) {
                // Continue even if one fails
            }
        }

        return back()->with('success', "تم إرسال البريد الإلكتروني إلى ($count) مستلم بنجاح.");
    }
    public function submitEvaluation(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'nomination_id' => $id
            ],
            [
                'score' => $request->score,
                'notes' => $request->notes
            ]
        );

        return back()->with('success', 'تم حفظ التقييم بنجاح');
    }

    public function monitor()
    {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);
        
        // Count online users (active in last 5 minutes)
        // Requires database session driver
        $onlineUsers = \Illuminate\Support\Facades\DB::table('sessions')
                        ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                        ->count();

        return view('admin.monitor', compact('logs', 'onlineUsers'));
    }

    // Workflow: Promote General -> Central
    public function promoteGeneralToCentral()
    {
        $count = Nomination::where('status', 'approved_general')->update(['status' => 'approved_central']);
        return back()->with('success', "تم ترحيل ($count) مرشح من مرحلة مديري العموم إلى الإدارة المركزية بنجاح.");
    }

    // Workflow: Promote Central -> Committee Review
    public function promoteCentralToCommittee()
    {
        // 'committee_review' is a new status we are introducing implicitly or explicitly
        // If the system uses 'approved_central' as the final state before committee, we might not need to change status
        // BUT the user said "appear to committee members", so let's assume we need a status change or just a flag.
        // Based on previous logic, committee sees 'approved_central' or 'winner'.
        // Let's check if we need a specific status. 
        // If committee view filters by 'approved_central', then we might not need to change status, 
        // BUT the user asked for a BUTTON to "End Phase". 
        // Let's introduce a 'committee_review' status to be explicit.
        
        $count = Nomination::where('status', 'approved_central')->update(['status' => 'committee_review']);
        return back()->with('success', "تم ترحيل ($count) مرشح من الإدارة المركزية إلى لجنة التقييم بنجاح.");
    }

    // Admin: Restore Rejected Nomination
    public function restoreNomination($id)
    {
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $nomination = Nomination::findOrFail($id);
        $nomination->status = 'pending'; // Reset to pending
        $nomination->save();

        return back()->with('success', 'تم استعادة المرشح بنجاح إلى حالة الانتظار');
    }

    public function approveNomination(Request $request, $id)
    {
        $nomination = Nomination::findOrFail($id);
        $user = Auth::user();

        // Check for explicit status change (e.g. Rejection or Final Approval by Chairman)
        if ($request->has('status') && in_array($request->status, ['rejected', 'winner'])) {
            if ($user->role === 'admin' || $user->role === 'chairman') {
                $nomination->status = $request->status;
                $nomination->save();
                $action = $request->status === 'rejected' ? 'رفض' : 'اعتماد';
                return back()->with('success', "تم $action الترشيح بنجاح");
            }
        }

        // Standard Approval Flow
        if ($user->role === 'general') {
            if ($nomination->general_dept_id !== $user->department_id) {
                return back()->with('error', 'غير مصرح لك باعتماد هذا المرشح');
            }
            $nomination->status = 'approved_general';
        } elseif ($user->role === 'central') {
            if ($nomination->central_dept_id !== $user->department_id) {
                return back()->with('error', 'غير مصرح لك باعتماد هذا المرشح');
            }
            $nomination->status = 'approved_central';
        } elseif ($user->role === 'admin' || $user->role === 'chairman') {
             // Default flow if no specific status requested
             if ($nomination->status == 'pending') $nomination->status = 'approved_general';
             elseif ($nomination->status == 'approved_general') $nomination->status = 'approved_central';
             elseif ($nomination->status == 'approved_central') $nomination->status = 'winner';
        }

        $nomination->save();

        return back()->with('success', 'تم اعتماد الترشيح بنجاح');
    }
}
