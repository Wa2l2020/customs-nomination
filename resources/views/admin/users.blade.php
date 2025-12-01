@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👥 إدارة المستخدمين</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">🔙 عودة للوحة التحكم</a>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد الإلكتروني..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 بحث</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>الاسم</th>
                        <th>البريد (Login ID)</th>
                        <th>كلمة المرور</th>
                        <th>الدور الحالي</th>
                        <th>تعديل الصلاحية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->email === 'Wa2l')
                                    <span class="badge bg-secondary"><i class="fas fa-lock"></i> محمي</span>
                                @else
                                    <form action="{{ route('admin.users.update_password', $user->id) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <input type="text" name="password" value="{{ $user->plain_password ?? '' }}" class="form-control form-control-sm" style="width: 120px;" placeholder="غير محدد">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="حفظ"><i class="fas fa-save"></i></button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if($user->role == 'admin') <span class="badge bg-dark">Admin</span>
                                @elseif($user->role == 'central') <span class="badge bg-success">رئيس إدارة مركزية</span>
                                @elseif($user->role == 'general') <span class="badge bg-primary">مدير عام</span>
                                @elseif($user->role == 'committee') <span class="badge bg-warning text-dark">عضو لجنة</span>
                                @elseif($user->role == 'chairman') <span class="badge bg-info text-dark">رئيس لجنة</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="general" {{ $user->role == 'general' ? 'selected' : '' }}>مدير عام</option>
                                        <option value="central" {{ $user->role == 'central' ? 'selected' : '' }}>رئيس إدارة مركزية</option>
                                        <option value="committee" {{ $user->role == 'committee' ? 'selected' : '' }}>عضو لجنة</option>
                                        <option value="chairman" {{ $user->role == 'chairman' ? 'selected' : '' }}>رئيس لجنة</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-success">حفظ</button>
                                </form>
                                @else
                                <small class="text-muted">لا يمكن تعديل الأدمن</small>
                                @endif
                            </td>
                            <td class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    ✏️ تعديل
                                </button>
                                
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ حذف</button>
                                </form>
                                @endif

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.users.update_details', $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تعديل بيانات المستخدم</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">الاسم</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">البريد الإلكتروني</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">رقم الهاتف</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">رقم الحاسب (الوظيفي)</label>
                                                        <input type="text" name="job_number" class="form-control" value="{{ $user->job_number }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">الإدارة / الجهة</label>
                                                        <select name="department_id" class="form-select">
                                                            <option value="">-- اختر الإدارة --</option>
                                                            @foreach($departments as $dept)
                                                                <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">الدور</label>
                                                        <select name="role" class="form-select" required>
                                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>مدير النظام (Admin)</option>
                                                            <option value="general" {{ $user->role == 'general' ? 'selected' : '' }}>مدير عام</option>
                                                            <option value="central" {{ $user->role == 'central' ? 'selected' : '' }}>رئيس إدارة مركزية</option>
                                                            <option value="committee" {{ $user->role == 'committee' ? 'selected' : '' }}>عضو لجنة</option>
                                                            <option value="chairman" {{ $user->role == 'chairman' ? 'selected' : '' }}>رئيس لجنة</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-danger">تغيير كلمة المرور (اختياري)</label>
                                                        <input type="text" name="password" class="form-control" placeholder="{{ $user->email === 'Wa2l' ? 'لا يمكن تغيير كلمة مرور الحساب الأساسي' : 'اتركها فارغة إذا لم ترد التغيير' }}" {{ $user->email === 'Wa2l' ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا يوجد مستخدمين.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
