@extends('layouts.master')

@section('styles')
<style>
    @media print {
        @page { size: A4; margin: 10mm; }
        body { font-size: 12px; background: white !important; }
        .container, .container-fluid { max-width: 100% !important; padding: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; margin-bottom: 10px !important; break-inside: avoid; }
        .card-header { background: #f0f0f0 !important; padding: 5px 10px !important; font-size: 14px !important; font-weight: bold !important; color: #000 !important; }
        .card-body { padding: 10px !important; }
        
        /* Compact Grid for Form Fields */
        .row.g-3 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .col-md-6 { width: 100% !important; }
        
        /* Hide non-essentials */
        .d-print-none, .btn, .alert-info, .alert-warning, input[type="file"], .text-muted.small { display: none !important; }
        
        /* Form Controls */
        .form-control, .form-select { border: none !important; border-bottom: 1px solid #ccc !important; padding: 0 !important; height: auto !important; background: transparent !important; }
        textarea.form-control { border: 1px solid #eee !important; resize: none; }
        
        /* Questions Compact */
        .questions-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .questions-grid { display: contents; }
        .question-item { break-inside: avoid; margin-bottom: 10px; }
        
        /* Header */
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .screen-header { display: none !important; }
    }
    .print-header { display: none; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="print-header">
            <div style="font-size: 30px; color: #003366; margin-bottom: 10px;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <!-- Static text removed as per request -->
        </div>
        <div class="text-center mb-4 screen-header">
            <h2 class="fw-bold">🏆 {{ $settings['nomination_page_title'] ?? 'استمارة ترشح لتكريم' }}</h2>
            <p class="text-muted" style="font-size: 1.2rem;">{{ $settings['nomination_page_subtitle'] ?? 'نظام الترشيحات والتكريم الإلكتروني 2025' }}</p>
        </div>

        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i>
            في إطار تعزيز قيم الجمارك المصرية من التميز والمعرفة والإبتكار وروح الفريق، وكذلك الإهتمام بالمورد البشرى لما له من دور هام فى تحقيق رؤيتنا من الريادة العالمية. لذا نهتم ونقدر جهود الكوادر البشرية لإنجازاتهم وإبتكارتهم ونزاهتهم فى العمل بما يحقق التميز فى أداء المنظومة الجمركية.
        </div>

        <form action="{{ route('nomination') }}" method="POST" enctype="multipart/form-data" id="nominationForm">
            @csrf
            
            <!-- 1. Candidate Info -->
            <div class="card mb-3">
                <div class="card-header fw-bold bg-light text-primary">📋 بيانات المرشح</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="required fw-bold">الاسم الرباعي</label>
                            <input type="text" name="employee_name" class="form-control" value="{{ old('employee_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">رقم الحاسب (Computer Number)</label>
                            <input type="text" name="job_number" class="form-control" value="{{ old('job_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">الدرجة الوظيفية</label>
                            <select name="job_grade" class="form-select" required>
                                <option value="">اختر الدرجة...</option>
                                <option value="اولي" {{ old('job_grade') == 'اولي' ? 'selected' : '' }}>اولي</option>
                                <option value="ثانية" {{ old('job_grade') == 'ثانية' ? 'selected' : '' }}>ثانية</option>
                                <option value="ثالثة" {{ old('job_grade') == 'ثالثة' ? 'selected' : '' }}>ثالثة</option>
                                <option value="رابعة" {{ old('job_grade') == 'رابعة' ? 'selected' : '' }}>رابعة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">المسمى الوظيفي</label>
                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">أعلى درجة علمية</label>
                            <input type="text" name="highest_degree" class="form-control" value="{{ old('highest_degree') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">الإدارة التي تعمل بها</label>
                            <input type="text" name="department_name" class="form-control" value="{{ old('department_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Department Selection -->
            <div class="card mb-3">
                <div class="card-header fw-bold bg-light text-primary">🏢 بيانات العمل</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="required fw-bold">الإدارة المركزية</label>
                            <select name="central_dept_id" id="centralSelect" class="form-select" required>
                                <option value="">اختر الإدارة المركزية</option>
                                @foreach($centralDepts as $cDept)
                                    <option value="{{ $cDept->id }}" data-children="{{ json_encode($cDept->children) }}" {{ old('central_dept_id') == $cDept->id ? 'selected' : '' }}>
                                        {{ $cDept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold">الإدارة العامة</label>
                            <select name="general_dept_id" id="generalSelect" class="form-select" required>
                                <option value="">اختر أولاً الإدارة المركزية</option>
                                <!-- Populated by JS, but we handle selection there too -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Category Selection -->
            <div class="card mb-3">
                <div class="card-header fw-bold bg-light text-primary">🎯 اختر فئة الترشيح</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="required fw-bold mb-2">الفئة</label>
                        <select name="category" id="categorySelect" class="form-select" onchange="loadQuestions()" required>
                            <option value="">-- اختر الفئة --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4. Dynamic Questions -->
            <div id="questionsContainer"></div>

            <!-- 5. Attachments -->
            <div class="card mb-3">
                <div class="card-header fw-bold bg-light text-primary">📎 المرفقات</div>
                <div class="card-body">
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle"></i>
                        يسمح فقط برفع ملفات بصيغة <strong>PDF</strong> أو <strong>JPG</strong>.
                        <br>
                        <strong>ملاحظة هامة:</strong> يجب ألا يتجاوز الحجم الإجمالي لجميع المرفقات <strong>30 ميجابايت</strong>.
                    </div>

                    <div class="mb-4">
                        <label class="required fw-bold d-block mb-2">1. بيان حالة وظيفية حديث</label>
                        <input type="file" name="job_status_file" class="filepond" accept=".pdf,.jpg,.jpeg" required>
                        <small class="text-muted">يرجى رفع ملف واحد فقط (PDF أو JPG).</small>
                    </div>

                    <div class="mb-3">
                        <label class="required fw-bold d-block mb-2">2. باقي المرفقات (الأدلة والوثائق)</label>
                        <textarea name="attachments_description" class="form-control mb-2" placeholder="وصف المرفقات التي تحمّل معك..." rows="2" required></textarea>
                        <input type="file" name="other_files[]" class="filepond" accept=".pdf,.jpg,.jpeg" multiple required>
                        <small class="text-muted">يمكنك اختيار أكثر من ملف.</small>
                    </div>
                </div>
            </div>

            <!-- 6. Terms & Conditions -->
            <div class="card mb-4 border-warning">
                <div class="card-body bg-light">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms_agreed" id="termsCheck" required {{ old('terms_agreed') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="termsCheck">
                            أوافق على الشروط والأحكام:
                        </label>
                        <p class="mt-2 text-muted small" style="white-space: pre-wrap;">{{ $settings['terms_text'] ?? 'أقر أنا المرشح بصحة البيانات...' }}</p>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center my-4">
                <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">✅ إرسال الترشيح</button>
                <button type="button" class="btn btn-warning btn-lg px-5" onclick="window.print()">🖨️ طباعة</button>
                <button type="reset" class="btn btn-secondary btn-lg px-5">🔄 مسح</button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; color: white;">
    <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
    <h4 class="fw-bold">جاري رفع المرفقات وإرسال البيانات...</h4>
    <p>يرجى الانتظار وعدم إغلاق الصفحة حتى ظهور رسالة التأكيد.</p>
</div>

@endsection

@section('scripts')
<script>
    // 0. Loading State Logic
    document.getElementById('nominationForm').addEventListener('submit', function(e) {
        // Show overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        // Disable button to prevent double submit
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '⏳ جاري الإرسال...';
    });

    // 1. Department Logic
    document.getElementById('centralSelect').addEventListener('change', function() {
        const children = JSON.parse(this.options[this.selectedIndex].dataset.children || '[]');
        const target = document.getElementById('generalSelect');
        const oldGeneral = "{{ old('general_dept_id') }}";
        
        target.innerHTML = '<option value="">اختر...</option>';
        children.forEach(c => {
            const selected = c.id == oldGeneral ? 'selected' : '';
            target.innerHTML += `<option value="${c.id}" ${selected}>${c.name}</option>`;
        });
    });

    // Trigger change if central dept was selected (old input)
    if ("{{ old('central_dept_id') }}") {
        document.getElementById('centralSelect').dispatchEvent(new Event('change'));
    }

    // 2. Dynamic Questions Logic
    // Generate JS object from DB data
    const categoriesData = {
        @foreach($categories as $cat)
        '{{ $cat->name }}': {
            @php
                $criteria = $cat->questions->groupBy('criterion');
            @endphp
            @foreach($criteria as $criterion => $questions)
            '{{ $criterion }}': [
                @foreach($questions as $q)
                { text: '{{ $q->text }}', type: '{{ $q->type }}' },
                @endforeach
            ],
            @endforeach
        },
        @endforeach
    };

    // Old answers passed from PHP
    const oldAnswers = @json(old('answers', []));

    function loadQuestions() {
        const category = document.getElementById('categorySelect').value;
        const container = document.getElementById('questionsContainer');
        
        if (!category) return;
        
        let html = '<div class="card mb-3"><div class="card-header fw-bold bg-light text-primary">❓ الأسئلة (بحد أقصى 300 كلمة لكل إجابة)</div><div class="card-body">';
        
        const criteriaData = categoriesData[category];
        
        if (criteriaData && Object.keys(criteriaData).length > 0) {
            let qCounter = 1;
            
            for (const [criterion, questions] of Object.entries(criteriaData)) {
                // Criterion Header
                if (criterion) {
                    html += `
                        <div class="alert alert-secondary py-2 fw-bold mb-3" style="grid-column: span 2;">
                            <i class="fas fa-tasks me-2"></i> معيار: ${criterion}
                        </div>
                    `;
                }
                
                html += '<div class="questions-grid" style="display: contents;">';
                
                questions.forEach(q => {
                    const key = `q${qCounter}`;
                    const oldVal = oldAnswers[key] || '';
                    
                    html += `
                        <div class="mb-3 ps-3 border-end border-3 border-primary question-item">
                            <label class="fw-bold mb-2 text-dark">${qCounter}: ${q.text}</label>
                    `;
                    
                    if (q.type === 'textarea') {
                        html += `
                            <textarea name="answers[${key}]" class="form-control" rows="4" placeholder="أجب بإيجاز..." onkeyup="updateWordCount(this)" required>${oldVal}</textarea>
                            <div class="text-end text-muted small mt-1 d-print-none"><span class="count">0</span> / 300 كلمة</div>
                        `;
                    } else {
                        html += `<input type="text" name="answers[${key}]" class="form-control" value="${oldVal}" required>`;
                    }
                    
                    html += `</div>`;
                    qCounter++;
                });
                
                html += '</div>';
            }
            
            html = html.replace('<div class="card-body">', '<div class="card-body"><div class="questions-wrapper">');
            html += '</div>';
        } else {
            html += '<p class="text-muted">لا توجد أسئلة محددة لهذه الفئة.</p>';
        }
        
        html += '</div></div>';
        container.innerHTML = html;

        // Update word counts for restored textareas
        document.querySelectorAll('textarea[onkeyup]').forEach(updateWordCount);
    }

    function updateWordCount(textarea) {
        const text = textarea.value.trim();
        const words = text.length > 0 ? text.split(/\s+/).length : 0;
        const countSpan = textarea.nextElementSibling.querySelector('.count');
        countSpan.textContent = Math.min(words, 300);
        
        if (words > 300) {
            textarea.classList.add('is-invalid');
            textarea.nextElementSibling.classList.add('text-danger');
        } else {
            textarea.classList.remove('is-invalid');
            textarea.nextElementSibling.classList.remove('text-danger');
        }
    }

    // Trigger loadQuestions if category was selected (old input)
    if ("{{ old('category') }}") {
        loadQuestions();
    }

    // 3. FilePond Initialization
    FilePond.registerPlugin(
        FilePondPluginImagePreview, 
        FilePondPluginFileValidateSize,
        FilePondPluginImageResize,
        FilePondPluginImageTransform
    );

    const inputElement1 = document.querySelector('input[name="job_status_file"]');
    const inputElement2 = document.querySelector('input[name="other_files[]"]');

    const commonOptions = {
        storeAsFile: true,
        credits: false,
        labelIdle: 'اسحب وأفلت الملفات هنا أو <span class="filepond--label-action">تصفح</span>',
        // Validation
        maxFileSize: '10MB',
        labelMaxFileSizeExceeded: 'الملف كبير جداً',
        labelMaxFileSize: 'الحد الأقصى هو {filesize}',
        acceptedFileTypes: ['application/pdf', 'image/jpeg', 'image/png'],
        labelFileTypeNotAllowed: 'نوع الملف غير مدعوم',
        fileValidateTypeLabelExpectedTypes: 'نتوقع {allButLastType} أو {lastType}',
        // Compression (Images Only)
        imageResizeTargetWidth: 1200,
        imageResizeMode: 'contain',
        imageTransformOutputQuality: 80,
        imageTransformOutputStripImageHead: true // Removes EXIF data to save space
    };

    FilePond.create(inputElement1, {
        ...commonOptions,
        maxFileSize: '10MB',
    });

    FilePond.create(inputElement2, {
        ...commonOptions,
        allowMultiple: true,
        maxFileSize: '20MB',
        maxTotalFileSize: '30MB',
    });
</script>
@endsection
