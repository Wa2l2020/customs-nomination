@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope"></i> إرسال بريد جماعي</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mass_email.send') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">المجموعة المستهدفة</label>
                            <select name="target_group" class="form-select" required>
                                <option value="all_candidates">جميع المرشحين</option>
                                <option value="approved">المقبولين فقط (Approved/Winners)</option>
                                <option value="rejected">المرفوضين فقط</option>
                                <option value="managers">المديرين (رؤساء الإدارات المركزية ومديري العموم)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">عنوان الرسالة</label>
                            <input type="text" name="subject" class="form-control" placeholder="مثال: هام بخصوص موعد المقابلة" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">محتوى الرسالة (HTML مدعوم)</label>
                            <textarea name="content" class="form-control" rows="8" placeholder="يمكنك كتابة نص عادي أو HTML هنا..." required></textarea>
                            <div class="form-text">يمكنك استخدام تنسيقات HTML بسيطة مثل &lt;b&gt;, &lt;br&gt;, &lt;p&gt;</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال الآن</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
