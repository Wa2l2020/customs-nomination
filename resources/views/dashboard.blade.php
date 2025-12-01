@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                📊 لوحة التحكم 
                <small class="text-muted fs-5">
                    ({{ Auth::user()->name }} - 
                    @if(Auth::user()->role == 'central')
                        رئيس الإدارة المركزية لـ {{ Auth::user()->department->name ?? '...' }}
                    @elseif(Auth::user()->role == 'general')
                        مدير عام لـ {{ Auth::user()->department->name ?? '...' }}
                    @elseif(Auth::user()->role == 'committee')
                        عضو لجنة الترشيح
                    @elseif(Auth::user()->role == 'chairman')
                        رئيس لجنة الترشيح
                    @else
                        {{ Auth::user()->role_label }}
                    @endif
                    )
                </small>
            </h2>
            <p class="text-muted">
                مرحباً بك في نظام إدارة الترشيحات
                @if(Auth::user()->last_login_at)
                    <span class="mx-2">|</span>
                    <small><i class="fas fa-clock"></i> آخر دخول: {{ Auth::user()->last_login_at->format('Y-m-d H:i') }}</small>
                @endif
            </p>
        </div>
        <div>
            @if(in_array(Auth::user()->role, ['committee', 'chairman']))
                <a href="{{ route('admin.stats') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-chart-pie me-1"></i> الإحصائيات
                </a>
            @endif
            <span class="badge bg-primary fs-6">{{ Auth::user()->role_label ?? Auth::user()->role }}</span>
        </div>
    </div>

    <!-- Committee Stats -->
    @if(in_array(Auth::user()->role, ['committee', 'chairman']))
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h5>الكل</h5>
                        <h3 class="fw-bold">{{ $nominations->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h5>تم التقييم</h5>
                        <h3 class="fw-bold">{{ $nominations->filter(fn($n) => $n->evaluations->where('user_id', Auth::id())->count() > 0)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow-sm">
                    <div class="card-body">
                        <h5>متبقي</h5>
                        <h3 class="fw-bold">{{ $nominations->filter(fn($n) => $n->evaluations->where('user_id', Auth::id())->count() == 0)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">تصفية حسب الفئة</label>
                    <select name="category" class="form-select">
                        <option value="">-- كل الفئات --</option>
                        <option value="الموظف المبتكر" {{ request('category') == 'الموظف المبتكر' ? 'selected' : '' }}>الموظف المبتكر</option>
                        <option value="الموظف المتميز" {{ request('category') == 'الموظف المتميز' ? 'selected' : '' }}>الموظف المتميز</option>
                        <option value="أفضل فريق عمل" {{ request('category') == 'أفضل فريق عمل' ? 'selected' : '' }}>أفضل فريق عمل</option>
                        <option value="النزاهة ومكافحة الفساد" {{ request('category') == 'النزاهة ومكافحة الفساد' ? 'selected' : '' }}>النزاهة ومكافحة الفساد</option>
                    </select>
                </div>

                @if(isset($generalDepts) && count($generalDepts) > 0)
                <div class="col-md-4">
                    <label class="form-label fw-bold">تصفية حسب الإدارة العامة</label>
                    <select name="general_dept_id" class="form-select">
                        <option value="">-- كل الإدارات --</option>
                        @foreach($generalDepts as $dept)
                            <option value="{{ $dept->id }}" {{ request('general_dept_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> تصفية</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    <!-- User Charts Section -->
    @if(isset($stats) && count($stats) > 0)
    <div class="row mb-4">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        @if(Auth::user()->role == 'general')
            <div class="col-md-6 mb-3">
                @include('partials.analytics_card', [
                    'title' => 'حالة الترشيحات في إدارتك',
                    'id' => 'statusChart',
                    'type' => 'pie',
                    'labels' => $stats['status_distribution']->keys(),
                    'data' => $stats['status_distribution']->values(),
                    'colors' => ['#ffc107', '#28a745', '#dc3545', '#0d6efd', '#198754'],
                    'showLegend' => true
                ])
            </div>
            <div class="col-md-6 mb-3">
                @include('partials.analytics_card', [
                    'title' => 'توزيع الفئات',
                    'id' => 'categoryChart',
                    'type' => 'doughnut',
                    'labels' => $stats['category_distribution']->keys(),
                    'data' => $stats['category_distribution']->values(),
                    'colors' => ['#003366', '#c5a017', '#28a745', '#dc3545', '#17a2b8'],
                    'showLegend' => true
                ])
            </div>
        @elseif(Auth::user()->role == 'central')
            <div class="col-md-6 mb-3">
                @include('partials.analytics_card', [
                    'title' => 'الترشيحات حسب الإدارة العامة',
                    'id' => 'deptChart',
                    'type' => 'bar',
                    'labels' => $stats['by_general_dept']->keys(),
                    'data' => $stats['by_general_dept']->values(),
                    'colors' => '#003366'
                ])
            </div>
            <div class="col-md-6 mb-3">
                @include('partials.analytics_card', [
                    'title' => 'حالة الترشيحات في إدارتك المركزية',
                    'id' => 'statusChart',
                    'type' => 'pie',
                    'labels' => $stats['status_distribution']->keys(),
                    'data' => $stats['status_distribution']->values(),
                    'colors' => ['#ffc107', '#28a745', '#dc3545', '#0d6efd', '#198754'],
                    'showLegend' => true
                ])
            </div>
        @elseif(Auth::user()->role == 'committee')
            <div class="col-md-12 mb-3">
                @include('partials.analytics_card', [
                    'title' => 'توزيع درجات تقييمك',
                    'id' => 'scoreChart',
                    'type' => 'bar',
                    'labels' => $stats['score_distribution']->keys(),
                    'data' => $stats['score_distribution']->values(),
                    'colors' => '#28a745',
                    'datasetLabel' => 'عدد المرشحين',
                    'description' => 'يوضح هذا الرسم توزيع الدرجات التي منحتها للمرشحين.'
                ])
            </div>
        @endif
    </div>
    @endif

    <!-- Nominations List -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">📋 قائمة الترشيحات الواردة</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">المرشح</th>
                            <th>
                                <a href="{{ route('dashboard', array_merge(request()->all(), ['sort_by' => 'category', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                    الفئة <i class="fas fa-sort{{ request('sort_by') == 'category' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }} text-muted small"></i>
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('dashboard', array_merge(request()->all(), ['sort_by' => 'general_dept', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                    الإدارة العامة <i class="fas fa-sort{{ request('sort_by') == 'general_dept' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }} text-muted small"></i>
                                </a>
                            </th>
                            <th>الحالة الحالية</th>
                            @if(in_array(Auth::user()->role, ['committee', 'chairman']))
                                <th>تقييمي</th>
                            @endif
                            @if(Auth::user()->role == 'chairman')
                                <th>متوسط التقييم</th>
                            @endif
                            <th>تاريخ التقديم</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nominations as $nom)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">
                                    {{ $nom->employee_name }}
                                    @if($nom->created_at->gt(now()->subDay()))
                                        <span class="badge bg-danger ms-2">جديد</span>
                                    @endif
                                </div>
                                <div class="small text-muted">{{ $nom->job_title }}</div>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $nom->category }}</span></td>
                            <td>{{ $nom->generalDept->name ?? '-' }}</td>
                            <td>
                                @if($nom->status == 'pending') <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @elseif($nom->status == 'approved_general') <span class="badge bg-primary">موافقة مدير عام</span>
                                @elseif($nom->status == 'approved_central') <span class="badge bg-success">موافقة رئيس إدارة مركزية</span>
                                @elseif($nom->status == 'winner') <span class="badge bg-gold text-dark">🏆 فائز نهائي</span>
                                @elseif($nom->status == 'rejected') <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </td>
                            @if(in_array(Auth::user()->role, ['committee', 'chairman']))
                                <td>
                                    @php
                                        $myEval = $nom->evaluations->where('user_id', Auth::id())->first();
                                    @endphp
                                    @if($myEval)
                                        <span class="badge bg-primary fs-6">{{ $myEval->score }} / 100</span>
                                        <button class="btn btn-sm btn-link text-muted" data-bs-toggle="modal" data-bs-target="#evalModal{{ $nom->id }}"><i class="fas fa-edit"></i></button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#evalModal{{ $nom->id }}">
                                            <i class="fas fa-star"></i> تقييم
                                        </button>
                                    @endif

                                    <!-- Evaluation Modal -->
                                    <div class="modal fade" id="evalModal{{ $nom->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.evaluate', $nom->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تقييم المرشح: {{ $nom->employee_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">الدرجة (من 100)</label>
                                                            <input type="number" name="score" class="form-control" min="0" max="100" value="{{ $myEval->score ?? '' }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">ملاحظات (اختياري)</label>
                                                            <textarea name="notes" class="form-control" rows="3">{{ $myEval->notes ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التقييم</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            @if(Auth::user()->role == 'chairman')
                                <td>
                                    @php
                                        $avgScore = $nom->evaluations->avg('score');
                                    @endphp
                                    @if($avgScore)
                                        <span class="badge bg-success fs-6">{{ number_format($avgScore, 1) }} / 100</span>
                                        <div class="small text-muted">({{ $nom->evaluations->count() }} تقييم)</div>
                                    @else
                                        <span class="text-muted small">لا يوجد تقييمات</span>
                                    @endif
                                </td>
                            @endif
                            <td>{{ $nom->created_at->format('Y-m-d') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('nomination.show', $nom->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    
                                    @if(Auth::user()->role == 'general' && $nom->status == 'pending')
                                        <form action="{{ route('nomination.status', $nom->id) }}" method="POST" class="d-inline">
                                            @csrf <input type="hidden" name="action" value="approve">
                                            <button class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من الاعتماد؟')">✅ اعتماد</button>
                                        </form>
                                        <form action="{{ route('nomination.status', $nom->id) }}" method="POST" class="d-inline">
                                            @csrf <input type="hidden" name="action" value="reject">
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الرفض؟')">❌ رفض</button>
                                        </form>
                                    @elseif(Auth::user()->role == 'central' && $nom->status == 'approved_general')
                                        <form action="{{ route('nomination.status', $nom->id) }}" method="POST" class="d-inline">
                                            @csrf <input type="hidden" name="action" value="approve">
                                            <button class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من الاعتماد النهائي؟')">✅ اعتماد نهائي</button>
                                        </form>
                                        <form action="{{ route('nomination.status', $nom->id) }}" method="POST" class="d-inline">
                                            @csrf <input type="hidden" name="action" value="reject">
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الرفض؟')">❌ رفض</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                                <p>لا توجد ترشيحات مطابقة للبحث حالياً.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
