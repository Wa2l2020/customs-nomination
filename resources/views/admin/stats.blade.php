@extends('layouts.admin')

@section('styles')
<style>
    @media print {
        @page { size: A4 portrait; margin: 10mm; } /* Switched to Portrait as requested for vertical layout */
        body { font-size: 12pt; background: white !important; -webkit-print-color-adjust: exact; font-family: 'Cairo', 'Segoe UI', sans-serif; }
        .container-fluid { padding: 0 !important; max-width: 100% !important; }
        
        /* Formal Header */
        .print-header { 
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #003366; 
            padding-bottom: 10px; 
            margin-bottom: 20px;
        }
        .print-logo img { height: 50px !important; width: auto !important; } 
        .print-title { text-align: center; flex-grow: 1; }
        .print-title h2 { font-size: 16pt !important; font-weight: 800 !important; color: #003366; margin-bottom: 5px; }
        .print-title h4 { font-size: 11pt !important; color: #444; font-weight: 600; margin-top: 0; }
        .print-meta { 
            text-align: left;
            font-size: 9pt; 
            color: #555; 
            line-height: 1.3;
            min-width: 140px;
        }
        
        /* Executive Summary */
        .exec-summary {
            border: 1px solid #003366;
            background-color: #f8f9fa !important;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: none;
        }
        .exec-summary h5 { color: #003366; font-weight: bold; margin-bottom: 5px; font-size: 12pt; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
        .exec-summary p { margin-bottom: 3px; font-size: 10pt; line-height: 1.4; }

        /* Grid Layout - Single Column for Charts */
        .print-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px; } /* Summary cards stay horizontal */
        
        /* Force charts to be stacked (Single Column) */
        .col-md-6, .col-12 { 
            width: 100% !important; 
            display: block !important; 
            margin-bottom: 20px !important; 
            page-break-inside: avoid; 
        }
        
        /* Cards & Charts */
        .card { border: 1px solid #ccc !important; box-shadow: none !important; margin: 0 !important; border-radius: 4px; }
        .card-header { background: #f0f0f0 !important; padding: 5px 10px !important; font-size: 11pt !important; font-weight: bold !important; color: #000 !important; border-bottom: 1px solid #ccc !important; }
        .card-body { padding: 10px !important; }
        
        /* Chart Sizing - Controlled Height */
        .chart-container { height: 180px !important; width: 100% !important; }
        canvas { max-height: 180px !important; width: 100% !important; object-fit: contain; }
        
        /* Description Text */
        .chart-desc { display: block !important; font-size: 9pt; color: #666; margin-top: 5px; font-style: italic; }
        
        /* Typography */
        .display-6 { font-size: 1.5rem !important; font-weight: 800; }
        .small { font-size: 9pt !important; }
        
        /* Hide elements */
        .no-print, .btn, .d-print-none, form, .navbar, .main-footer { display: none !important; }
    }
</style>
@endsection

@section('admin-content')
<div class="container-fluid">
    <!-- Print Header -->
    <div class="d-none d-print-flex print-header">
        <div class="print-logo">
            @if(!empty($settings['logo_url']))
                <img src="{{ $settings['logo_url'] }}" alt="Logo">
            @else
                <i class="fas fa-shield-alt fa-4x text-primary"></i>
            @endif
        </div>
        <div class="print-title">
            <h2>{{ $settings['site_title'] ?? 'مصلحة الجمارك المصرية' }}</h2>
            <h4>تقرير مؤشرات الأداء والترشيحات ({{ date('Y') }})</h4>
        </div>
        <div class="print-meta">
            <div><strong>تاريخ التقرير:</strong> {{ date('Y/m/d') }}</div>
            <div><strong>وقت الطباعة:</strong> {{ date('H:i A') }}</div>
            <div><strong>المستخرج:</strong> {{ Auth::user()->name }}</div>
        </div>
    </div>

    <!-- Executive Summary (Print Only) -->
    <div class="d-none d-print-block exec-summary">
        <h5>📊 ملخص تنفيذي</h5>
        <p>
            يستعرض هذا التقرير حالة نظام الترشيحات والتكريم، حيث بلغ إجمالي عدد الترشيحات المسجلة <strong>{{ $stats['total'] }}</strong> ترشيحاً.
            تم اعتماد <strong>{{ $stats['approved_central'] + $stats['winners'] }}</strong> ترشيحاً بشكل نهائي من قبل الإدارات المركزية، 
            بينما لا يزال <strong>{{ $stats['pending'] }}</strong> ترشيحاً قيد المراجعة والتدقيق.
        </p>
        <p>
            تُظهر المؤشرات أن الفئة الأكثر نشاطاً هي <strong>{{ $stats['by_category']->sortDesc()->keys()->first() ?? 'غير محدد' }}</strong>، 
            مما يعكس اهتمام الموظفين بهذا المجال. كما يوضح التقرير التوزيع الجغرافي والوظيفي للمشاركين، مما يساعد في اتخاذ القرارات التحسينية للدورات القادمة.
        </p>
    </div>

    <!-- Page Header (Screen Only) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h2 class="h4 fw-bold text-gray-800">
            <i class="fas fa-chart-pie me-2 text-primary"></i>الإحصائيات والتقارير
        </h2>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print me-2"></i>طباعة التقرير
        </button>
    </div>

    <!-- Filters (Screen Only) -->
    <div class="card mb-4 d-print-none">
        <div class="card-body">
            <form action="{{ route('admin.stats') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">نطاق العرض</label>
                    <select name="filter_type" class="form-select">
                        <option value="all" {{ request('filter_type') == 'all' ? 'selected' : '' }}>عرض الجميع</option>
                        <option value="winners" {{ request('filter_type') == 'winners' ? 'selected' : '' }}>الفائزون فقط</option>
                        <option value="approved" {{ request('filter_type') == 'approved' ? 'selected' : '' }}>المعتمدون (نهائي)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">الفئة</label>
                    <select name="category" class="form-select">
                        <option value="">كل الفئات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">الإدارة المركزية</label>
                    <select name="central_dept_id" class="form-select">
                        <option value="">كل الإدارات المركزية</option>
                        @foreach($centralDepts as $dept)
                            <option value="{{ $dept->id }}" {{ request('central_dept_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلتر
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Print Settings (Screen Only) -->
    <div class="card mb-4 d-print-none border-secondary">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-cog me-2"></i>خيارات الطباعة
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="summary-cards" checked id="checkSummary">
                        <label class="form-check-label" for="checkSummary">ملخص الأرقام</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="timelineChart" checked id="checkTimeline">
                        <label class="form-check-label" for="checkTimeline">الرسم البياني الزمني</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="scoresChart" checked id="checkScores">
                        <label class="form-check-label" for="checkScores">متوسط التقييم</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="categoryChart" checked id="checkCategory">
                        <label class="form-check-label" for="checkCategory">توزيع الفئات</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="statusChart" checked id="checkStatus">
                        <label class="form-check-label" for="checkStatus">حالة الترشيحات</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="jobGradeChart" checked id="checkJobGrade">
                        <label class="form-check-label" for="checkJobGrade">التوزيع الوظيفي</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="centralChart" checked id="checkCentral">
                        <label class="form-check-label" for="checkCentral">مشاركات الإدارات المركزية</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input print-toggle" type="checkbox" data-target="dept-list" checked id="checkDeptList">
                        <label class="form-check-label" for="checkDeptList">قائمة الإدارات العامة</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 print-grid-3" id="summary-cards">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body text-center p-2">
                    <h2 class="display-6 fw-bold mb-0">{{ $stats['total'] }}</h2>
                    <p class="mb-0 small">إجمالي الترشيحات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body text-center p-2">
                    <h2 class="display-6 fw-bold mb-0">{{ $stats['winners'] }}</h2>
                    <p class="mb-0 small">الفائزون</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100 shadow-sm">
                <div class="card-body text-center p-2">
                    <h2 class="display-6 fw-bold mb-0">{{ $stats['pending'] }}</h2>
                    <p class="mb-0 small">قيد الانتظار</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-print-none">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body text-center p-2">
                    <h2 class="display-6 fw-bold mb-0">{{ $stats['approved_central'] }}</h2>
                    <p class="mb-0 small">معتمد (رئيس قطاع)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights Alert -->
    @if(!empty($insights))
    <div class="alert alert-light border-primary mb-3 d-flex align-items-center shadow-sm p-2 d-print-none">
        <i class="fas fa-lightbulb text-warning fa-lg me-2"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">رؤى وتحليلات</h6>
            <ul class="mb-0 ps-3 small">
                @foreach($insights as $insight)
                    <li>{!! $insight !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Charts Grid -->
    <div class="row">
        <!-- Timeline Chart -->
        <div class="col-12 mb-3" id="timelineChart-container">
            @include('partials.analytics_card', [
                'title' => 'تدفق الترشيحات (30 يوم)',
                'id' => 'timelineChart',
                'type' => 'line',
                'labels' => $stats['nominations_over_time']->keys(),
                'data' => $stats['nominations_over_time']->values(),
                'colors' => 'rgba(0, 51, 102, 0.1)',
                'borderColors' => '#003366',
                'datasetLabel' => 'عدد الترشيحات',
                'height' => '180'
            ])
            <p class="chart-desc d-none d-print-block">يوضح الرسم البياني أعلاه معدل تقديم الترشيحات اليومي خلال آخر 30 يوماً، مما يساعد في تتبع فترات الذروة.</p>
        </div>

        <!-- Category Scores Chart -->
        <div class="col-md-6 mb-3" id="scoresChart-container">
            @include('partials.analytics_card', [
                'title' => 'متوسط التقييم',
                'id' => 'scoresChart',
                'type' => 'bar',
                'labels' => $stats['avg_score_by_category']->keys(),
                'data' => $stats['avg_score_by_category']->values(),
                'colors' => '#28a745',
                'datasetLabel' => 'المتوسط',
                'height' => '180'
            ])
            <p class="chart-desc d-none d-print-block">يعرض هذا الرسم متوسط درجات التقييم لكل فئة من فئات الجائزة، مما يبرز الفئات ذات الأداء الأعلى.</p>
        </div>

        <!-- Category Distribution Chart -->
        <div class="col-md-6 mb-3" id="categoryChart-container">
            @include('partials.analytics_card', [
                'title' => 'توزيع الفئات',
                'id' => 'categoryChart',
                'type' => 'doughnut',
                'labels' => $stats['by_category']->keys(),
                'data' => $stats['by_category']->values(),
                'colors' => ['#003366', '#c5a017', '#28a745', '#dc3545', '#17a2b8'],
                'showLegend' => true,
                'height' => '180'
            ])
            <p class="chart-desc d-none d-print-block">يوضح الرسم نسب توزيع المرشحين على مختلف فئات الجائزة، لتحديد الفئات الأكثر إقبالاً.</p>
        </div>

        <!-- Status Chart -->
        <div class="col-md-6 mb-3" id="statusChart-container">
            @include('partials.analytics_card', [
                'title' => 'حالة الترشيحات',
                'id' => 'statusChart',
                'type' => 'pie',
                'labels' => ['انتظار', 'فائز', 'مرفوض', 'معتمد (عام)', 'معتمد (مركزي)'],
                'data' => [
                    $stats['pending'],
                    $stats['winners'],
                    $stats['rejected'],
                    $stats['approved_general'],
                    $stats['approved_central']
                ],
                'colors' => ['#ffc107', '#28a745', '#dc3545', '#0d6efd', '#198754'],
                'showLegend' => true,
                'height' => '180'
            ])
             <p class="chart-desc d-none d-print-block">نظرة عامة على الوضع الحالي لجميع الترشيحات في النظام، موضحاً نسب القبول والرفض والانتظار.</p>
        </div>

        <!-- Job Grade Chart -->
        <div class="col-md-6 mb-3" id="jobGradeChart-container">
            @include('partials.analytics_card', [
                'title' => 'التوزيع الوظيفي',
                'id' => 'jobGradeChart',
                'type' => 'bar',
                'labels' => $stats['by_job_grade']->keys(),
                'data' => $stats['by_job_grade']->values(),
                'colors' => '#17a2b8',
                'height' => '180'
            ])
             <p class="chart-desc d-none d-print-block">تحليل لتوزيع المرشحين حسب درجاتهم الوظيفية، مما يعكس مستوى المشاركة عبر مختلف المستويات الإدارية.</p>
        </div>

        <!-- Central Dept Chart -->
        <div class="col-12 mb-3" id="centralChart-container">
            @include('partials.analytics_card', [
                'title' => 'مشاركات الإدارات المركزية',
                'id' => 'centralChart',
                'type' => 'bar',
                'labels' => $stats['by_central']->keys(),
                'data' => $stats['by_central']->values(),
                'colors' => '#003366',
                'datasetLabel' => 'العدد',
                'height' => '180'
            ])
             <p class="chart-desc d-none d-print-block">مقارنة بين عدد الترشيحات المقدمة من كل إدارة مركزية، لتحديد القطاعات الأكثر تفاعلاً.</p>
        </div>
    </div>

    <!-- General Dept List -->
    <div class="card mb-3" id="dept-list">
        <div class="card-header fw-bold py-1">أكثر الإدارات العامة مشاركة (أعلى 10)</div>
        <div class="card-body p-2">
            <div class="row g-2">
                @foreach($stats['by_general'] as $dept => $count)
                <div class="col-6 col-md-6">
                    <div class="d-flex justify-content-between align-items-center border rounded p-1 px-2">
                        <small class="text-truncate" style="max-width: 85%;">{{ $dept }}</small>
                        <span class="badge bg-info text-dark rounded-pill">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    Chart.register(ChartDataLabels);
    Chart.defaults.set('plugins.datalabels', {
        color: '#fff',
        font: { weight: 'bold', size: 10 },
        formatter: (value) => value > 0 ? value : ''
    });
    Chart.defaults.maintainAspectRatio = false;

    // Print Toggles
    document.querySelectorAll('.print-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const targetId = this.dataset.target;
            let targetEl;
            
            if (targetId === 'summary-cards' || targetId === 'dept-list') {
                targetEl = document.getElementById(targetId);
            } else {
                // For charts, we target the container
                targetEl = document.getElementById(targetId + '-container');
            }

            if (targetEl) {
                if (this.checked) {
                    targetEl.classList.remove('d-print-none');
                } else {
                    targetEl.classList.add('d-print-none');
                }
            }
        });
    });
</script>
@endsection
