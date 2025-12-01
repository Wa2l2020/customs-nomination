<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Nomination;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NominationSubmitted;

class SystemController extends Controller
{
    public function checkStatus(Request $request)
    {
        $request->validate([
            'job_number' => 'required|string'
        ]);

        $nomination = Nomination::where('job_number', $request->job_number)->latest()->first();

        if ($nomination) {
            return response()->json([
                'found' => true,
                'name' => $nomination->employee_name,
                'category' => $nomination->category,
                'status' => $nomination->status,
                'date' => $nomination->created_at->format('Y-m-d')
            ]);
        }

        return response()->json(['found' => false]);
    }

    // --- Registration (The Builder) ---
    public function showRegister()
    {
        $deadline = Setting::where('key', 'nomination_deadline')->value('value');
        if ($deadline && now()->gt($deadline)) {
            return view('deadline_passed', ['message' => 'عذراً، انتهت فترة التسجيل والترشيح.']);
        }
        $centralDepts = Department::whereNull('parent_id')->get();
        return view('register', compact('centralDepts'));
    }

    public function register(Request $request)
    {
        $deadline = Setting::where('key', 'nomination_deadline')->value('value');
        if ($deadline && now()->gt($deadline)) {
            return back()->with('error', 'عذراً، انتهت فترة التسجيل.');
        }

        $request->validate([
            'reg_type' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'job_number' => 'required',
            'phone' => 'required',
            'job_grade' => 'required',
            'job_title' => 'required',
            'highest_degree' => 'required',
            'department_name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $deptId = null;
            $role = '';

            if ($request->reg_type === 'central') {
                // Central Admin: Create a new Root Department
                $dept = Department::create([
                    'name' => $request->central_dept_name_text,
                    'location' => $request->location ?? 'المقر الرئيسي', // Default location if not provided
                    'parent_id' => null
                ]);
                $deptId = $dept->id;
                $role = 'central';

            } elseif ($request->reg_type === 'general') {
                // General Admin: Create a Child Department
                $dept = Department::create([
                    'name' => $request->general_dept_name_text,
                    'location' => $request->location ?? 'المقر الرئيسي',
                    'parent_id' => $request->central_dept_id
                ]);
                $deptId = $dept->id;
                $role = 'general';

            } else {
                // Committee Member
                // Validate Committee Code
                $settingsCode = \App\Models\Setting::where('key', 'committee_registration_password')->value('value') ?? '1232';
                if ($request->committee_code !== $settingsCode) {
                    throw new \Exception('كود تسجيل اللجنة غير صحيح.');
                }
                $role = $request->committee_role; // committee or chairman
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'plain_password' => $request->password,
                'role' => $role,
                'job_number' => $request->job_number,
                'phone' => $request->phone,
                'department_id' => $deptId,
                'job_grade' => $request->job_grade,
                'job_title' => $request->job_title,
                'highest_degree' => $request->highest_degree,
                'department_name' => $request->department_name,
            ]);

            DB::commit();

            // Send Welcome Email
            try {
                Mail::to($user->email)->send(new \App\Mail\UserRegistered($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Registration email failed: ' . $e->getMessage());
            }

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'register',
                'description' => 'New user registered: ' . $user->role,
                'ip_address' => request()->ip(),
            ]);

            $successMessage = \App\Models\Setting::where('key', 'registration_success_message')->value('value') 
                ?? 'تم إرسال بريد إلكتروني لتأكيد تسجيلك. يرجى التحقق من صندوق الوارد.';

            return view('registration_success', compact('successMessage'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // --- Nomination (The Consumer) ---
    public function showNomination()
    {
        $deadline = Setting::where('key', 'nomination_deadline')->value('value');
        if ($deadline && now()->gt($deadline)) {
            return view('deadline_passed', ['message' => 'عذراً، انتهت فترة الترشيح.']);
        }
        $centralDepts = Department::whereNull('parent_id')->with('children')->get();
        $settings = Setting::pluck('value', 'key');
        $categories = \App\Models\Category::with('questions')->get();
        return view('nomination', compact('centralDepts', 'settings', 'categories'));
    }

    public function submitNomination(Request $request)
    {
        $deadline = Setting::where('key', 'nomination_deadline')->value('value');
        if ($deadline && now()->gt($deadline)) {
            return back()->with('error', 'عذراً، انتهت فترة الترشيح.');
        }

        $validated = $request->validate([
            'employee_name' => 'required',
            'job_number' => 'required',
            'job_title' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'central_dept_id' => 'required',
            'general_dept_id' => 'required',
            'category' => 'required|exists:categories,name',
            'job_grade' => 'required',
            'highest_degree' => 'required',
            'department_name' => 'required',
            'answers' => 'required|array',
            'job_status_file' => 'required|file|mimes:pdf,jpg,jpeg|max:5120',
            'other_files.*' => 'file|mimes:pdf,jpg,jpeg|max:10240',
            'terms_agreed' => 'accepted'
        ]);

        // Validate Total File Size (Max 30MB)
        $totalSize = 0;
        if ($request->hasFile('job_status_file')) {
            $totalSize += $request->file('job_status_file')->getSize();
        }
        if ($request->hasFile('other_files')) {
            foreach ($request->file('other_files') as $file) {
                $totalSize += $file->getSize();
            }
        }

        if ($totalSize > 30 * 1024 * 1024) { // 30MB in bytes
            return back()->withInput()->withErrors(['attachments' => 'عذراً، الحجم الإجمالي للمرفقات يتجاوز الحد المسموح به (30 ميجابايت).']);
        }

        $rawAnswers = $request->input('answers', []);
        $categoryName = $validated['category'];
        $category = \App\Models\Category::where('name', $categoryName)->with('questions')->first();

        $formattedAnswers = [];
        if ($category) {
            $questions = $category->questions; // Assumes order is preserved or sorted by ID
            foreach ($rawAnswers as $key => $value) {
                // Extract index from "q1", "q2" -> 1, 2
                $index = (int)filter_var($key, FILTER_SANITIZE_NUMBER_INT) - 1;
                
                if (isset($questions[$index])) {
                    $questionText = $questions[$index]->text;
                    $formattedAnswers[$questionText] = $value;
                } else {
                    // Fallback if question not found
                    $formattedAnswers[$key] = $value;
                }
            }
        } else {
            $formattedAnswers = $rawAnswers;
        }

        // Handle File Uploads (Structured Storage)
        $attachments = [];
        $jobNumber = $validated['job_number'];
        
        // Storage Configuration
        $driver = Setting::where('key', 'storage_driver')->value('value') ?? 'local';
        
        // Dynamic Config for Cloud Drivers
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
        
        // Helper function to store file
        $storeFile = function($file, $prefix) use ($jobNumber, $driver) {
            // Sanitize filename: remove special chars, keep dots and dashes
            $originalName = $file->getClientOriginalName();
            $safeName = preg_replace('/[^a-zA-Z0-9\-\._\p{Arabic}]/u', '_', $originalName);
            
            // Format: JobNumber_Filename
            $filename = $jobNumber . '_' . $safeName;
            
            // Ensure folder is strictly the job number
            $folder = 'nominations/' . $jobNumber; 
            $path = $folder . '/' . $filename;

            try {
                if ($driver !== 'local') {
                    // For cloud, we might just use the path relative to root
                    \Illuminate\Support\Facades\Storage::disk($driver)->put($path, file_get_contents($file));
                    return $path;
                }
            } catch (\Exception $e) {
                // Fallback to local if cloud fails
                \Illuminate\Support\Facades\Log::error("Cloud upload failed ($driver): " . $e->getMessage());
            }

            // Default Local Storage
            return $file->storeAs($folder, $filename, 'public');
        };

        if ($request->hasFile('job_status_file')) {
            $path = $storeFile($request->file('job_status_file'), 'job_status');
            $attachments[] = ['type' => 'job_status', 'path' => $path];
        }

        if ($request->hasFile('other_files')) {
            foreach ($request->file('other_files') as $file) {
                $path = $storeFile($file, 'attachment');
                $attachments[] = ['type' => 'other', 'path' => $path];
            }
        }

        // Create Nomination
        $nomination = Nomination::create([
            'employee_name' => $validated['employee_name'],
            'job_number' => $validated['job_number'],
            'job_title' => $validated['job_title'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'central_dept_id' => $validated['central_dept_id'],
            'general_dept_id' => $validated['general_dept_id'],
            'category' => $validated['category'],
            'job_grade' => $validated['job_grade'],
            'highest_degree' => $validated['highest_degree'],
            'department_name' => $validated['department_name'],
            'answers' => $formattedAnswers,
            'attachments' => $attachments,
            'status' => 'pending'
        ]);

        // Send Confirmation Email
        try {
            Mail::to($validated['email'])->send(new NominationSubmitted($nomination));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email sending failed: ' . $e->getMessage());
        }

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(), // Might be null if public, but usually protected
            'action' => 'submit_nomination',
            'description' => 'Nomination submitted for: ' . $nomination->employee_name,
            'ip_address' => request()->ip(),
        ]);

        return view('nomination_success');
    }

    // --- Dashboard ---
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $query = Nomination::query();

        // 1. Role Scoping
        if ($user->role === 'general') {
            $query->where('general_dept_id', $user->department_id);
        } elseif ($user->role === 'central') {
            $query->where('central_dept_id', $user->department_id)
                  ->whereIn('status', ['approved_general', 'approved_central', 'winner', 'rejected']);
        } elseif ($user->role === 'committee' || $user->role === 'chairman') {
            $query->whereIn('status', ['approved_central', 'winner']);
        }

        // 2. Filters
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->general_dept_id && ($user->role === 'central' || $user->role === 'chairman')) {
            $query->where('general_dept_id', $request->general_dept_id);
        }

        // 3. Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Allow sorting by related models
        if ($sortField === 'central_dept') {
            $query->join('departments as c_dept', 'nominations.central_dept_id', '=', 'c_dept.id')
                  ->orderBy('c_dept.name', $sortOrder)
                  ->select('nominations.*'); // Avoid column collision
        } elseif ($sortField === 'general_dept') {
            $query->join('departments as g_dept', 'nominations.general_dept_id', '=', 'g_dept.id')
                  ->orderBy('g_dept.name', $sortOrder)
                  ->select('nominations.*');
        } elseif (in_array($sortField, ['category', 'created_at', 'status'])) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->latest();
        }

        $nominations = $query->with(['generalDept', 'centralDept', 'evaluations'])->get();
        
        // Data for Filters
        $generalDepts = [];
        if ($user->role === 'central') {
            $generalDepts = Department::where('parent_id', $user->department_id)->get();
        } elseif ($user->role === 'chairman') {
            $generalDepts = Department::whereNotNull('parent_id')->get();
        }

        // Dashboard Stats for Charts
        $stats = [];
        if ($user->role === 'general') {
            $stats['status_distribution'] = $nominations->groupBy('status')->map->count();
            $stats['category_distribution'] = $nominations->groupBy('category')->map->count();
        } elseif ($user->role === 'central') {
            $stats['by_general_dept'] = $nominations->groupBy(fn($n) => $n->generalDept->name ?? 'غير محدد')->map->count();
            $stats['status_distribution'] = $nominations->groupBy('status')->map->count();
        } elseif ($user->role === 'committee') {
            $myEvals = \App\Models\Evaluation::where('user_id', $user->id)->pluck('score');
            $stats['score_distribution'] = $myEvals->groupBy(fn($s) => floor($s/10)*10 . '-' . (floor($s/10)*10 + 9))->map->count()->sortKeys();
        }

        return view('dashboard', compact('nominations', 'generalDepts', 'stats'));
    }
    
    public function show($id)
    {
        $nomination = Nomination::with(['centralDept', 'generalDept'])->findOrFail($id);
        
        // Fetch Managers Names
        $generalManager = \App\Models\User::where('role', 'general')
                            ->where('department_id', $nomination->general_dept_id)
                            ->first();
                            
        $centralHead = \App\Models\User::where('role', 'central')
                            ->where('department_id', $nomination->central_dept_id)
                            ->first();

        return view('admin.nomination_show', compact('nomination', 'generalManager', 'centralHead'));
    }

    public function updateStatus(Request $request, $id)
    {
        $nom = Nomination::findOrFail($id);
        $action = $request->action;
        $role = Auth::user()->role;
        
        // Deadline Check
        if ($role == 'general') {
            $deadline = Setting::where('key', 'general_manager_deadline')->value('value');
            if ($deadline && now()->gt($deadline)) {
                return back()->with('error', 'عذراً، انتهت فترة الاعتماد لمدير العموم.');
            }
        } elseif ($role == 'central') {
            $deadline = Setting::where('key', 'central_admin_deadline')->value('value');
            if ($deadline && now()->gt($deadline)) {
                return back()->with('error', 'عذراً، انتهت فترة الاعتماد لرئيس الإدارة المركزية.');
            }
        }
        
        if ($action == 'approve') {
            if ($role == 'general') $nom->status = 'approved_general';
            if ($role == 'central') $nom->status = 'approved_central';
        } else {
            $nom->status = 'rejected';
        }
        
        $nom->save();
        return back()->with('success', 'تم تحديث الحالة');
    }
}
