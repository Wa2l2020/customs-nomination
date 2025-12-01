@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2>📄 تفاصيل الترشيح #{{ $nomination->id }}</h2>
        <div>
            <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> طباعة</button>
            <button onclick="window.close()" class="btn btn-outline-danger">إغلاق</button>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="printableArea">
        <!-- Official Header (Visible in Print) -->
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-none d-print-block">
            <div class="row align-items-center text-center">
                <div class="col-4 text-end">
                    <h6 class="fw-bold mb-1">وزارة المالية</h6>
                    <h6 class="fw-bold">مصلحة الجمارك</h6>
                </div>
                <div class="col-4">
                    <img src="{{ \App\Models\Setting::where('key', 'logo_url')->value('value') ?? 'https://www.customs.gov.eg/images/logo.png' }}" alt="Logo" style="height: 80px; opacity: 0.8;" referrerpolicy="no-referrer">
                </div>
                <div class="col-4 text-start">
                    <h6 class="fw-bold mb-1">نظام الترشيح والتميز</h6>
                    <small class="text-muted">تاريخ الطباعة: {{ date('Y-m-d') }}</small>
                </div>
            </div>
            <hr class="mt-4 border-primary" style="opacity: 0.5;">
            
            <!-- Title (Moved & Resized) -->
            <div class="text-center mb-2">
                <h6 class="fw-bold text-primary mb-1">استمارة ترشيح {{ $nomination->category }}</h6>
            </div>

            <!-- Preamble (Terms) -->
            <div class="alert alert-light border text-center mt-2 mb-2 p-2 small text-muted d-print-block" style="font-size: 0.85rem;">
                فى إطار تعزيز قيم الجمارك المصرية من التميز والمعرفة والإبتكار وروح الفريق ، وكذلك الإهتمام بالمورد البشرى لما له من دور هام فى تحقيق رؤيتنا من الريادة العالمية . لذا نهتم ونقدر جهود الكوادر البشرية لإنجازاتهم وإبتكارتهم و ونزاهتهم فى العمل بما يحقق التميز فى أداء المنظومة الجمركية .
            </div>
        </div>

        <div class="card-body px-4 py-3">
            <!-- Status Badge Only -->
            <div class="text-center mb-4 d-print-none">
                <h4 class="fw-bold text-primary mb-1">استمارة ترشيح {{ $nomination->category }}</h4>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fs-6">{{ $nomination->status == 'pending' ? 'قيد الانتظار' : $nomination->status }}</span>
            </div>

            <!-- Employee Info Grid -->
            <div class="row g-2 mb-4 p-3 rounded border" style="background-color: #f8f9fa;">
                <div class="col-md-12 mb-1">
                    <h6 class="fw-bold border-bottom pb-1 text-secondary"><i class="fas fa-user-tie me-2"></i> بيانات المرشح</h6>
                </div>
                
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">الاسم الرباعي</span>
                        <span class="fw-bold">{{ $nomination->employee_name }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">رقم الحاسب</span>
                        <span class="fw-bold">{{ $nomination->job_number }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">المسمى الوظيفي</span>
                        <span class="fw-bold">{{ $nomination->job_title }}</span>
                    </div>
                </div>
                
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">الدرجة الوظيفية</span>
                        <span class="fw-bold">{{ $nomination->job_grade ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">الإدارة</span>
                        <span class="fw-bold">{{ $nomination->department_name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">الإدارة العامة</span>
                        <span class="fw-bold">{{ $nomination->generalDept->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">الإدارة المركزية</span>
                        <span class="fw-bold">{{ $nomination->centralDept->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">رقم الهاتف</span>
                        <span class="fw-bold">{{ $nomination->phone ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted small">البريد الإلكتروني</span>
                        <span class="fw-bold">{{ $nomination->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Answers Section -->
            <h6 class="fw-bold border-bottom pb-1 text-secondary mt-4 mb-3"><i class="fas fa-question-circle me-2"></i> إجابات الاستمارة</h6>
            @if($nomination->answers)
                @foreach($nomination->answers as $key => $value)
                    @if($key != 'attachments_description' && $key != 'terms_agreed')
                        <div class="mb-3 page-break-inside-avoid">
                            <div class="fw-bold text-dark mb-1">{{ $key }}</div>
                            <div class="p-2 border rounded bg-light text-dark" style="white-space: pre-wrap;">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</div>
                        </div>
                    @elseif($key == 'attachments_description' && !empty($value))
                             <div class="mb-3 page-break-inside-avoid">
                                <div class="fw-bold text-dark mb-1 d-flex align-items-center" style="background-color: #fff3cd; padding: 8px; border-right: 4px solid #ffc107; border-radius: 4px; font-size: 0.9rem;">
                                    <i class="fas fa-file-alt text-warning me-2 ms-2"></i>
                                    وصف المرفقات
                                </div>
                                <div class="p-2 border rounded bg-white shadow-sm" style="font-size: 0.9rem;">
                                    {{ $value }}
                                </div>
                            </div>
                    @endif
                @endforeach
            @endif

            <!-- Attachments Section -->
            <h6 class="fw-bold border-bottom pb-1 text-secondary mt-4 mb-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-paperclip me-2"></i> المرفقات</span>
                @if($nomination->cloud_folder_link)
                    <a href="{{ $nomination->cloud_folder_link }}" target="_blank" class="btn btn-sm btn-info text-white shadow-sm">
                        <i class="fas fa-cloud me-1"></i> استعراض المرفقات (سحابي)
                    </a>
                @endif
            </h6>
            
            @if($nomination->attachments && count($nomination->attachments) > 0)
                <!-- Screen View (Table) -->
                <div class="table-responsive d-print-none">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary">اسم الملف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nomination->attachments as $attachment)
                                <tr>
                                    <td>
                                        @php
                                            $pathParts = explode('/', $attachment['path'] ?? $attachment['file_path'] ?? '');
                                            $filename = end($pathParts);
                                            $folder = prev($pathParts);
                                        @endphp
                                        <a href="{{ route('admin.attachments.view', ['folder' => $folder, 'filename' => $filename]) }}" target="_blank" class="text-decoration-none text-dark d-block">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            {{ $filename }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Print View (Grid) -->
                <div class="d-none d-print-block attachments-section">
                    @php
                        $attCount = count($nomination->attachments);
                        $gridClass = $attCount > 8 ? 'dense' : '';
                    @endphp
                    <div class="attachments-grid {{ $gridClass }}">
                        @foreach($nomination->attachments as $attachment)
                            @php
                                $pathParts = explode('/', $attachment['path'] ?? $attachment['file_path'] ?? '');
                                $filename = end($pathParts);
                            @endphp
                            <div class="attachment-item">
                                <i class="fas fa-file small"></i> {{ $filename }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(!$nomination->cloud_folder_link)
                <p class="text-muted small">لا توجد مرفقات.</p>
            @endif

            <div class="mt-4 pt-3 border-top page-break-inside-avoid">
                <div class="row text-center">
                    <!-- General Manager Signature -->
                    <div class="col-6">
                        @if(in_array($nomination->status, ['approved_general', 'approved_central', 'winner']))
                            <h6 class="fw-bold mb-1 small">إعتماد {{ $nomination->generalDept->name ?? 'الإدارة العامة' }}</h6>
                            <p class="mb-0 fw-bold">{{ $generalManager->name ?? 'مدير عام' }}</p>
                            <small class="text-muted d-block">{{ $nomination->updated_at->format('Y-m-d') }}</small>
                        @else
                            <h6 class="fw-bold mb-1 small text-muted">إعتماد {{ $nomination->generalDept->name ?? 'الإدارة العامة' }}</h6>
                            <div class="text-muted fst-italic py-1 small">-- قيد الاعتماد --</div>
                        @endif
                    </div>

                    <!-- Central Head Signature -->
                    <div class="col-6">
                        @if(in_array($nomination->status, ['approved_central', 'winner']))
                            <h6 class="fw-bold mb-1 small">إعتماد {{ $nomination->centralDept->name ?? 'الإدارة المركزية' }}</h6>
                            <p class="mb-0 fw-bold">{{ $centralHead->name ?? 'رئيس الإدارة المركزية' }}</p>
                            <small class="text-muted d-block">{{ $nomination->updated_at->format('Y-m-d') }}</small>
                        @else
                            <h6 class="fw-bold mb-1 small text-muted">إعتماد {{ $nomination->centralDept->name ?? 'الإدارة المركزية' }}</h6>
                            <div class="text-muted fst-italic py-1 small">-- قيد الاعتماد --</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 0.5cm;
        }
        body {
            font-size: 10pt;
            background-color: white !important;
        }
        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid #000 !important;
            padding-top: 0 !important;
            padding-bottom: 5px !important;
        }
        .card-body {
            padding: 10px !important;
        }
        .no-print, .d-print-none {
            display: none !important;
        }
        .d-print-grid {
            display: grid !important;
        }
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }
        a[href]:after {
            content: none !important;
        }
        /* Compact spacing */
        h3, h4, h5, h6 { margin-bottom: 5px !important; }
        p { margin-bottom: 5px !important; }
        .mb-3, .mb-4, .mb-5 { margin-bottom: 10px !important; }
        .py-3, .py-4 { padding-top: 5px !important; padding-bottom: 5px !important; }
        hr { margin: 10px 0 !important; }

        /* Attachments Grid Logic */
        .attachments-section {
            margin-top: 10px;
            border: 1px solid #000;
            padding: 5px;
        }
        .attachments-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Default 4 columns */
            gap: 5px;
        }
        .attachments-grid.dense {
            grid-template-columns: repeat(6, 1fr); /* Increase columns if many files */
            font-size: 8pt; /* Decrease font size */
        }
        .attachment-item {
            border: 1px solid #ccc;
            padding: 2px 5px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            text-align: center;
        }
    }
</style>
@endsection
