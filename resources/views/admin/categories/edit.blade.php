@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>✏️ تعديل الفئة: {{ $category->name }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">عودة</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Edit Category Info -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">بيانات الفئة</div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">اسم الفئة</label>
                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">وصف</label>
                            <textarea name="description" class="form-control" rows="4">{{ $category->description }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تحديث البيانات</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manage Questions -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                    <span>❓ الأسئلة والمعايير</span>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="fas fa-plus"></i> إضافة سؤال
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نص السؤال</th>
                                <th>النوع</th>
                                <th>حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->questions as $q)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::limit($q->text, 80) }}</td>
                                <td><span class="badge bg-secondary">{{ $q->type }}</span></td>
                                <td>
                                    <form action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('حذف هذا السؤال؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">لا توجد أسئلة مضافة لهذه الفئة بعد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.categories.questions.store', $category->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة سؤال جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نص السؤال / المعيار</label>
                        <textarea name="text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع الإجابة</label>
                        <select name="type" class="form-select">
                            <option value="textarea">نص طويل (Textarea)</option>
                            <option value="text">نص قصير (Input)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
