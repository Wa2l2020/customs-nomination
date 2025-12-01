@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📤 تصدير البيانات والنسخ الاحتياطي</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">🔙 عودة للوحة التحكم</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-5">
                        <i class="fas fa-database fa-4x text-primary mb-3"></i>
                        <h4 class="fw-bold">تصدير بيانات النظام</h4>
                        <p class="text-muted">يمكنك تصدير بيانات الترشيحات كملف Excel أو أخذ نسخة احتياطية كاملة من قاعدة البيانات.</p>
                    </div>

                    <div class="row g-4 justify-content-center mb-5">
                        <!-- Excel Export -->
                        <div class="col-md-4">
                            <div class="card text-center h-100 shadow-sm border-primary">
                                <div class="card-body">
                                    <div class="mb-3 text-primary">
                                        <i class="fas fa-file-excel fa-3x"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">تصدير Excel شامل</h5>
                                    <p class="card-text text-muted small">تصدير جميع بيانات النظام (ترشيحات، مستخدمين، إدارات) في ملف Excel واحد.</p>
                                    <form action="{{ route('admin.export.system') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="excel">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-download me-2"></i> تحميل ملف Excel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- SQL Backup -->
                        <div class="col-md-4">
                            <div class="card text-center h-100 shadow-sm border-success">
                                <div class="card-body">
                                    <div class="mb-3 text-success">
                                        <i class="fas fa-database fa-3x"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">نسخة احتياطية (SQL)</h5>
                                    <p class="card-text text-muted small">تحميل نسخة كاملة من قاعدة البيانات بصيغة SQL للحفظ والاسترجاع.</p>
                                    <form action="{{ route('admin.export.system') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="sql">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-download me-2"></i> تحميل ملف SQL
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Archive -->
                        <div class="col-md-4">
                            <div class="card text-center h-100 shadow-sm border-info">
                                <div class="card-body">
                                    <div class="mb-3 text-info">
                                        <i class="fas fa-file-archive fa-3x"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">أرشيف المرفقات</h5>
                                    <p class="card-text text-muted small">تحميل جميع المرفقات في ملف مضغوط (ZIP) لتفريغ المساحة.</p>
                                    <a href="{{ route('admin.export.attachments') }}" class="btn btn-info text-white w-100">
                                        <i class="fas fa-file-archive me-2"></i> تحميل الأرشيف (ZIP)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 text-start">
                        <h5 class="alert-heading"><i class="fas fa-info-circle"></i> ملاحظات هامة:</h5>
                        <ul class="mb-0">
                            <li>ملف <strong>Excel</strong> يحتوي على كافة بيانات الترشيحات والإجابات والتقييمات.</li>
                            <li>ملف <strong>SQL</strong> يحتوي على نسخة كاملة من قاعدة البيانات ويمكن استخدامه لاستعادة النظام.</li>
                            <li>ملف <strong>ZIP</strong> يحتوي على كافة المرفقات مرتبة داخل مجلدات بأرقام الحاسب.</li>
                        </ul>
                    </div>

                    <hr class="my-5">

                    <div class="mb-4">
                        <i class="fas fa-upload fa-4x text-danger mb-3"></i>
                        <h4 class="fw-bold text-danger">استعادة نسخة احتياطية</h4>
                        <p class="text-muted">يمكنك استعادة النظام من ملف نسخة احتياطية (SQL) سابق.</p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form action="{{ route('admin.restore') }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded bg-light">
                                @csrf
                                <div class="mb-3 text-start">
                                    <label for="backup_file" class="form-label fw-bold">ملف النسخة الاحتياطية (.sql)</label>
                                    <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".sql" required>
                                </div>
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('⚠️ تحذير: هذه العملية ستقوم بحذف جميع البيانات الحالية واستبدالها بالبيانات الموجودة في الملف.\n\nهل أنت متأكد من المتابعة؟')">
                                    <i class="fas fa-trash-restore"></i> استعادة النظام
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="mb-4">
                        <i class="fas fa-tools fa-4x text-secondary mb-3"></i>
                        <h4 class="fw-bold text-secondary">أدوات المطورين</h4>
                        <p class="text-muted">أدوات للاختبار وإعادة تعيين النظام. استخدمها بحذر.</p>
                    </div>

                    <div class="row justify-content-center gap-3">
                        <div class="col-md-5">
                            <form action="{{ route('admin.seed') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 py-3" onclick="return confirm('هل أنت متأكد؟ سيتم إضافة بيانات وهمية للاختبار.')">
                                    <i class="fas fa-random me-2"></i> ملء بيانات وهمية (Seed)
                                </button>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <form action="{{ route('admin.reset') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-dark w-100 py-3" onclick="return confirm('⚠️ خطر: سيتم مسح جميع البيانات (الترشيحات، المستخدمين، المرفقات) ما عدا حسابات الأدمن.\n\nهل أنت متأكد تماماً؟')">
                                    <i class="fas fa-skull me-2"></i> تصفير النظام (Reset)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
