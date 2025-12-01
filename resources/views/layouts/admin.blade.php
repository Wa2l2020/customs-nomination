@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-sm rounded-3 ms-3 py-3 d-print-none" style="min-height: 80vh;">
            <div class="position-sticky">
                <ul class="nav flex-column gap-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.monitor') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.monitor') }}">
                            <i class="fas fa-desktop me-2"></i> المراقبة الحية
                        </a>
                    </li>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>إدارة الترشيحات</span>
                    </h6>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.nominations') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.nominations') }}">
                            <i class="fas fa-list-alt me-2"></i> الترشيحات
                        </a>
                    </li>
                    @if(Auth::user()->role !== 'chairman')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.attachments.index') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.attachments.index') }}">
                            <i class="fas fa-paperclip me-2"></i> إدارة المرفقات
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->role !== 'chairman')
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>المستخدمين والإعدادات</span>
                    </h6>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.users') }}">
                            <i class="fas fa-users me-2"></i> المستخدمين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-tags me-2"></i> الفئات والمعايير
                        </a>
                    </li>
                    @endif

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>التقارير والأدوات</span>
                    </h6>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.stats') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.stats') }}">
                            <i class="fas fa-chart-pie me-2"></i> الإحصائيات
                        </a>
                    </li>
                    @if(Auth::user()->role !== 'chairman')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.export') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.export') }}">
                            <i class="fas fa-file-excel me-2"></i> تصدير البيانات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mass_email') ? 'active bg-primary text-white rounded' : 'text-dark' }}" href="{{ route('admin.mass_email') }}">
                            <i class="fas fa-envelope me-2"></i> البريد الجماعي
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-9 px-md-4">
            @yield('admin-content')
        </main>
    </div>
    </div>
</div>

<!-- Global Print Footer -->
<div class="fixed-bottom d-none d-print-block text-center p-2" style="background: white; border-top: 1px solid #ddd;">
    <small class="text-muted">
        {{ \App\Models\Setting::where('key', 'print_footer_text')->value('value') ?? 'نظام الترشيحات والتكريم - مصلحة الجمارك المصرية' }}
    </small>
</div>

@endsection
