@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary"><i class="fas fa-desktop me-2"></i> لوحة المراقبة الحية</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i> عودة للوحة التحكم
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body text-center">
                    <h1 class="display-4 fw-bold">{{ $onlineUsers }}</h1>
                    <p class="lead mb-0"><i class="fas fa-users me-2"></i> المتواجدون الآن</p>
                    <small class="text-white-50">نشط خلال آخر 5 دقائق</small>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-3">آخر نشاط</h5>
                    @if($logs->count() > 0)
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                @if($logs->first()->action == 'login')
                                    <i class="fas fa-sign-in-alt fa-2x text-success"></i>
                                @elseif($logs->first()->action == 'register')
                                    <i class="fas fa-user-plus fa-2x text-info"></i>
                                @else
                                    <i class="fas fa-file-alt fa-2x text-warning"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $logs->first()->description }}</h6>
                                <small class="text-muted">{{ $logs->first()->created_at->diffForHumans() }} بواسطة {{ $logs->first()->user->name ?? 'زائر' }}</small>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">لا يوجد نشاط مسجل بعد.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-secondary"><i class="fas fa-list-alt me-2"></i> سجل الأحداث</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الوقت</th>
                        <th>المستخدم</th>
                        <th>الحدث</th>
                        <th>الوصف</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap" dir="ltr">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($log->user)
                                    <span class="fw-bold">{{ $log->user->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $log->user->role_label }}</small>
                                @else
                                    <span class="text-muted">زائر / محذوف</span>
                                @endif
                            </td>
                            <td>
                                @if($log->action == 'login')
                                    <span class="badge bg-success">تسجيل دخول</span>
                                @elseif($log->action == 'register')
                                    <span class="badge bg-info text-dark">تسجيل جديد</span>
                                @elseif($log->action == 'submit_nomination')
                                    <span class="badge bg-warning text-dark">ترشيح</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>{{ $log->description }}</td>
                            <td><code class="text-muted">{{ $log->ip_address }}</code></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">لا توجد سجلات لعرضها.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
