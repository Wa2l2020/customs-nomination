@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4">🛠️ لوحة تحكم مدير النظام</h2>

    <!-- Quick Links & Navigation -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">🚀 روابط سريعة</h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.nominations') }}" class="btn btn-primary"><i class="fas fa-list"></i> استعراض الترشيحات</a>
                        <a href="{{ route('admin.export') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> تصدير Excel</a>
                        <a href="{{ route('admin.mass_email') }}" class="btn btn-dark"><i class="fas fa-envelope"></i> بريد جماعي</a>
                        <a href="{{ route('admin.users') }}" class="btn btn-info text-white"><i class="fas fa-users"></i> إدارة المستخدمين</a>
                        <a href="{{ route('admin.stats') }}" class="btn btn-warning text-dark"><i class="fas fa-chart-pie"></i> الإحصائيات</a>
                        <div class="vr"></div>
                        <a href="{{ route('nomination') }}" target="_blank" class="btn btn-outline-secondary">📄 استمارة الترشيح</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الترشيحات</h5>
                    <p class="card-text display-6">{{ $stats['total_nominations'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">قيد الانتظار</h5>
                    <p class="card-text display-6">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">الفائزون</h5>
                    <p class="card-text display-6">{{ $stats['winners'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">المستخدمين</h5>
                    <p class="card-text display-6">{{ $stats['users'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    @include('partials.charts')

    <!-- Review Section: Approved Nominations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">✅ الترشيحات المعتمدة (جاهزة للمراجعة النهائية)</h5>
                    <span class="badge bg-white text-success">{{ $stats['approved_central'] }} ترشيح</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">هذه الترشيحات تمت الموافقة عليها من رؤساء الإدارات المركزية وتنتظر اعتماد اللجنة النهائية.</p>
                    <a href="{{ route('admin.nominations', ['status' => 'approved_central']) }}" class="btn btn-outline-success">
                        عرض ومراجعة القائمة <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Settings Form -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header fw-bold">⚙️ إعدادات النظام</div>
                <div class="card-body">
                    <form action="{{ route('admin.settings') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">عنوان الموقع</label>
                                <input type="text" name="site_title" class="form-control" value="{{ $settings['site_title'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عنوان صفحة الترشيح</label>
                                <input type="text" name="nomination_page_title" class="form-control" value="{{ $settings['nomination_page_title'] ?? 'استمارة ترشح لتكريم' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">العنوان الفرعي لصفحة الترشيح</label>
                                <input type="text" name="nomination_page_subtitle" class="form-control" value="{{ $settings['nomination_page_subtitle'] ?? 'نظام الترشيحات والتكريم الإلكتروني 2025' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">نص تذييل الطباعة (Footer Text)</label>
                                <input type="text" name="print_footer_text" class="form-control" value="{{ $settings['print_footer_text'] ?? '' }}" placeholder="مثال: هذا المستند سري ومخصص للاستخدام الداخلي فقط">
                                <small class="text-muted">سيظهر هذا النص في أسفل كل صفحة عند الطباعة.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رابط الشعار (Logo URL)</label>
                                <input type="text" name="logo_url" class="form-control" value="{{ $settings['logo_url'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">لون الثيم (Hex)</label>
                                <input type="color" name="theme_color" class="form-control form-control-color" value="{{ $settings['theme_color'] ?? '#003366' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رابط الدعم الفني</label>
                                <input type="text" name="support_url" class="form-control" value="{{ $settings['support_url'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحد الأقصى لفئات الإدارة المركزية</label>
                                <input type="number" name="max_categories_central" class="form-control" value="{{ $settings['max_categories_central'] ?? 5 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحد الأقصى لفئات الإدارة العامة</label>
                                <input type="number" name="max_categories_general" class="form-control" value="{{ $settings['max_categories_general'] ?? 3 }}">
                            </div>
                            
                            <div class="col-12"><hr></div>
                            <div class="col-12"><h6 class="fw-bold text-primary">📅 المواعيد النهائية (Deadlines)</h6></div>
                            
                            <div class="col-md-4">
                                <label class="form-label">آخر موعد للترشيح</label>
                                <input type="datetime-local" name="nomination_deadline" class="form-control" value="{{ $settings['nomination_deadline'] ?? '' }}">
                                <small class="text-muted">بعد هذا التاريخ لن يتم قبول ترشيحات جديدة.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">آخر موعد لمدير العموم</label>
                                <input type="datetime-local" name="general_manager_deadline" class="form-control" value="{{ $settings['general_manager_deadline'] ?? '' }}">
                                <small class="text-muted">لن يتمكن المدير العام من الاعتماد بعد هذا التاريخ.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">آخر موعد لرئيس الإدارة المركزية</label>
                                <input type="datetime-local" name="central_admin_deadline" class="form-control" value="{{ $settings['central_admin_deadline'] ?? '' }}">
                                <small class="text-muted">لن يتمكن رئيس الإدارة المركزية من الاعتماد بعد هذا التاريخ.</small>
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-md-6">
                                <label class="form-label text-danger fw-bold">كود تسجيل اللجنة</label>
                                <input type="text" name="committee_registration_password" class="form-control border-danger" value="{{ $settings['committee_registration_password'] ?? '1232' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">نص الشروط والأحكام</label>
                                <textarea name="terms_text" class="form-control" rows="4">{{ $settings['terms_text'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">محتوى رسالة الترحيب (Welcome Email)</label>
                                <textarea name="welcome_email_body" class="form-control" rows="4">{{ $settings['welcome_email_body'] ?? '' }}</textarea>
                                <small class="text-muted">استخدم {name} لاسم المستخدم و {password} لكلمة المرور.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">رسالة نجاح التسجيل (تظهر في الموقع)</label>
                                <textarea name="registration_success_message" class="form-control" rows="3">{{ $settings['registration_success_message'] ?? 'تم إرسال بريد إلكتروني لتأكيد تسجيلك. يرجى التحقق من صندوق الوارد.' }}</textarea>
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-12"><h6 class="fw-bold text-primary">📝 محتوى الصفحة الرئيسية</h6></div>
                            <div class="col-12">
                                <label class="form-label">محتوى تبويب "الإرشادات" (يدعم HTML)</label>
                                <textarea name="instructions_content" class="form-control" rows="5" dir="rtl">{{ $settings['instructions_content'] ?? '' }}</textarea>
                                <small class="text-muted">يمكنك استخدام تنسيقات HTML مثل &lt;b&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;br&gt;.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">محتوى تبويب "عن الفريق" (يدعم HTML)</label>
                                <textarea name="about_team_content" class="form-control" rows="5" dir="rtl">{{ $settings['about_team_content'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <hr>
                                <h5 class="fw-bold text-primary"><i class="fas fa-envelope"></i> إعدادات البريد الإلكتروني (SMTP)</h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMTP Port</label>
                                <input type="text" name="mail_port" class="form-control" value="{{ $settings['mail_port'] ?? '587' }}" placeholder="587">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username (Email)</label>
                                <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password (App Password)</label>
                                <input type="password" name="mail_password" class="form-control" value="{{ $settings['mail_password'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Encryption</label>
                                <select name="mail_encryption" class="form-select">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">From Address</label>
                                <input type="text" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] ?? 'admin@example.com' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">From Name</label>
                                <input type="text" name="mail_from_name" class="form-control" value="{{ $settings['mail_from_name'] ?? 'Nomination System' }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">إيميلات يدوية (للتأكيد/Testing)</label>
                                <textarea name="manual_emails" class="form-control" rows="2" placeholder="email1@example.com, email2@example.com">{{ $settings['manual_emails'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Google Sheet ID (للنسخ الاحتياطي)</label>
                                <input type="text" name="google_sheet_id" class="form-control" value="{{ $settings['google_sheet_id'] ?? '' }}">
                            </div>
                            <div class="col-12">
                                <hr>
                                <h5 class="fw-bold text-primary"><i class="fas fa-cloud"></i> إعدادات التخزين السحابي</h5>
                                <div class="alert alert-info small">
                                    <strong>ملاحظة هامة:</strong> لكي يقوم السيرفر برفع الملفات تلقائياً، يجب توفر <strong>بيانات الربط (API Credentials)</strong> وليس مجرد رابط المجلد.
                                    <br>
                                    سيقوم النظام تلقائياً بإنشاء مجلد لكل مرشح برقم الحاسب الخاص به.
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">اختر مزود الخدمة</label>
                                <select name="storage_driver" id="storageDriver" class="form-select" onchange="toggleStorageFields()">
                                    <option value="local" {{ ($settings['storage_driver'] ?? '') == 'local' ? 'selected' : '' }}>سيرفر محلي (Local Storage)</option>
                                    <option value="google" {{ ($settings['storage_driver'] ?? '') == 'google' ? 'selected' : '' }}>Google Drive</option>
                                    <option value="onedrive" {{ ($settings['storage_driver'] ?? '') == 'onedrive' ? 'selected' : '' }}>Microsoft OneDrive</option>
                                    <option value="dropbox" {{ ($settings['storage_driver'] ?? '') == 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                </select>
                            </div>

                            <!-- Google Drive Fields -->
                            <div id="googleFields" class="storage-fields row g-3" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label">Client ID</label>
                                    <input type="text" name="google_client_id" class="form-control" value="{{ $settings['google_client_id'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Client Secret</label>
                                    <input type="text" name="google_client_secret" class="form-control" value="{{ $settings['google_client_secret'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Refresh Token</label>
                                    <input type="text" name="google_refresh_token" class="form-control" value="{{ $settings['google_refresh_token'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Folder ID (المجلد الرئيسي)</label>
                                    <input type="text" name="google_folder_id" class="form-control" value="{{ $settings['google_folder_id'] ?? '' }}">
                                </div>
                            </div>

                            <!-- OneDrive Fields -->
                            <div id="onedriveFields" class="storage-fields row g-3" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label">Client ID</label>
                                    <input type="text" name="onedrive_client_id" class="form-control" value="{{ $settings['onedrive_client_id'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Client Secret</label>
                                    <input type="text" name="onedrive_client_secret" class="form-control" value="{{ $settings['onedrive_client_secret'] ?? '' }}">
                                </div>
                            </div>

                            <!-- Dropbox Fields -->
                            <div id="dropboxFields" class="storage-fields row g-3" style="display: none;">
                                <div class="col-md-12">
                                    <label class="form-label">Access Token</label>
                                    <input type="text" name="dropbox_token" class="form-control" value="{{ $settings['dropbox_token'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">💾 حفظ الإعدادات</button>
                    </form>
                    
                    <hr>
                    <form action="{{ route('admin.sync') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">🔄 مزامنة الملفات المحلية</h6>
                                <small class="text-muted">رفع الملفات المحفوظة محلياً إلى السحابة (عند عودة الاتصال).</small>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i> بدء المزامنة</button>
                        </div>
    </div>
</div>

<script>
    function toggleStorageFields() {
        const driver = document.getElementById('storageDriver').value;
        document.querySelectorAll('.storage-fields').forEach(el => el.style.display = 'none');
        
        if (driver === 'google') document.getElementById('googleFields').style.display = 'flex';
        if (driver === 'onedrive') document.getElementById('onedriveFields').style.display = 'flex';
        if (driver === 'dropbox') document.getElementById('dropboxFields').style.display = 'flex';
    }
    
    // Run on load
    document.addEventListener('DOMContentLoaded', toggleStorageFields);
</script>
@endsection
