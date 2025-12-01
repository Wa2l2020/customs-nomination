@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📋 استعراض الترشيحات</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">🔙 عودة للوحة التحكم</a>
    </div>
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.nominations') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو رقم الحاسب" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- كل الحالات --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ قيد الانتظار</option>
                        <option value="approved_general" {{ request('status') == 'approved_general' ? 'selected' : '' }}>✅ موافقة مدير عام</option>
                        <option value="approved_central" {{ request('status') == 'approved_central' ? 'selected' : '' }}>✅✅ موافقة رئيس الإدارة المركزية</option>
                        <option value="winner" {{ request('status') == 'winner' ? 'selected' : '' }}>🏆 فائز</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">-- كل الفئات --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="central_dept_id" class="form-select">
                        <option value="">-- كل الإدارات المركزية --</option>
                        @foreach($centralDepts as $dept)
                            <option value="{{ $dept->id }}" {{ request('central_dept_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 بحث</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Workflow Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            @if(auth()->user()->role == 'admin')
                <form action="{{ route('admin.workflow.general_to_central') }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إنهاء مرحلة مديري العموم وترحيل كافة المرشحين المعتمدين؟')">
                    @csrf
                    <button type="submit" class="btn btn-warning text-dark fw-bold w-100 mb-2">
                        <i class="fas fa-forward me-2"></i> إنهاء مرحلة مديري العموم (ترحيل للإدارة المركزية)
                    </button>
                </form>
            @endif
        </div>
        <div class="col-md-6">
            @if(auth()->user()->role == 'admin')
                <form action="{{ route('admin.workflow.central_to_committee') }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إنهاء مرحلة الإدارة المركزية وترحيل كافة المرشحين للجنة التقييم؟')">
                    @csrf
                    <button type="submit" class="btn btn-info text-dark fw-bold w-100 mb-2">
                        <i class="fas fa-gavel me-2"></i> إنهاء مرحلة الإدارة المركزية (ترحيل للجنة التقييم)
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>رقم الحاسب</th>
                        <th>الاسم</th>
                        <th>الفئة</th>
                        <th>الإدارة المركزية</th>
                        <th>الإدارة العامة</th>
                        <th>الحالة</th>
                        <th>التقييم</th>
                        <th>تاريخ التقديم</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nominations as $nom)
                        <tr>
                            <td>{{ $nom->id }}</td>
                            <td>{{ $nom->job_number }}</td>
                            <td class="fw-bold">{{ $nom->employee_name }}</td>
                            <td><span class="badge bg-info text-dark">{{ $nom->category }}</span></td>
                            <td>{{ $nom->centralDept->name ?? '-' }}</td>
                            <td>{{ $nom->generalDept->name ?? '-' }}</td>
                            <td>
                                @if($nom->status == 'pending') <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @elseif($nom->status == 'approved_general') <span class="badge bg-primary">موافقة مدير عام</span>
                                @elseif($nom->status == 'approved_central') <span class="badge bg-success">موافقة رئيس الإدارة المركزية</span>
                                @elseif($nom->status == 'committee_review') <span class="badge bg-info">مرحلة التقييم</span>
                                @elseif($nom->status == 'winner') <span class="badge bg-gold text-dark">🏆 فائز نهائي</span>
                                @elseif($nom->status == 'rejected') <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $nom->score_avg ?? 0 }}%</span>
                            </td>
                            <td>{{ $nom->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('nomination.show', $nom->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">👁️ عرض</a>
                                    
                                    @if(auth()->user()->role == 'committee' && $nom->status == 'committee_review')
                                        <button class="btn btn-sm btn-outline-warning" onclick="openEvalModal({{ $nom->id }}, '{{ $nom->employee_name }}')">⭐ تقييم</button>
                                    @endif

                                    @if(auth()->user()->role == 'chairman')
                                        @if($nom->status != 'rejected')
                                            <form action="{{ route('admin.approve', $nom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الاعتماد النهائي؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="winner">
                                                <button type="submit" class="btn btn-sm btn-success">✅ اعتماد</button>
                                            </form>
                                            <form action="{{ route('admin.approve', $nom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الرفض؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-danger">❌ رفض</button>
                                            </form>
                                        @endif
                                    @endif

                                    @if(auth()->user()->role == 'admin' && $nom->status == 'rejected')
                                        <form action="{{ route('admin.nomination.restore', $nom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من استعادة هذا المرشح؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary">♻️ استعادة</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">لا توجد ترشيحات مطابقة للبحث.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $nominations->links() }}
        </div>
    </div>
</div>

<!-- Evaluation Modal -->
<div class="modal fade" id="evalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="POST" id="evalForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تقييم المرشح: <span id="evalName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">الدرجة (من 100)</label>
                        <input type="number" name="score" class="form-control" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
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

<script>
    function openEvalModal(id, name) {
        document.getElementById('evalName').innerText = name;
        document.getElementById('evalForm').action = `/admin/nomination/${id}/evaluate`;
        new bootstrap.Modal(document.getElementById('evalModal')).show();
    }
</script>
@endsection
