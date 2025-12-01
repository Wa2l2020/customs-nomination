@extends('layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> تسجيل حساب جديد</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('register') }}" method="POST" id="regForm">
                    @csrf
                    
                    <!-- Registration Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-2">نوع التسجيل</label>
                        <select name="reg_type" id="reg_type" class="form-select form-select-lg" onchange="toggleFields()">
                            <option value="central" selected>رئيس إدارة مركزية</option>
                            <option value="general">مدير عام</option>
                            <option value="committee">لجنة التحكيم</option>
                        </select>
                    </div>

                    <!-- Common Fields -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الاسم الرباعي</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">رقم الحاسب (Computer Number)</label>
                            <input type="text" name="job_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <!-- New Fields -->
                        <div class="col-md-6">
                            <label class="form-label required">الدرجة الوظيفية</label>
                            <select name="job_grade" class="form-select" required>
                                <option value="">اختر الدرجة...</option>
                                <option value="اولي">اولي</option>
                                <option value="ثانية">ثانية</option>
                                <option value="ثالثة">ثالثة</option>
                                <option value="رابعة">رابعة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">المسمى الوظيفي</label>
                            <input type="text" name="job_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">أعلى درجة علمية</label>
                            <input type="text" name="highest_degree" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">الإدارة التي تعمل بها</label>
                            <input type="text" name="department_name" class="form-control" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Central Dept Fields -->
                    <div id="central_fields">
                        <h5 class="text-primary mb-3">بيانات الإدارة المركزية</h5>
                        <div class="mb-3">
                            <label class="form-label required">اسم الإدارة المركزية</label>
                            <input type="text" name="central_dept_name_text" class="form-control" id="central_dept_name">
                        </div>
                    </div>

                    <!-- General Dept Fields -->
                    <div id="general_fields" style="display:none;">
                        <h5 class="text-primary mb-3">بيانات الإدارة العامة</h5>
                        <div class="mb-3">
                            <label class="form-label required">تتبع إدارة مركزية</label>
                            <select name="central_dept_id" class="form-select" id="general_parent_id">
                                <option value="">اختر الإدارة المركزية...</option>
                                @foreach($centralDepts as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">اسم الإدارة العامة</label>
                            <input type="text" name="general_dept_name_text" class="form-control" id="general_dept_name">
                        </div>
                    </div>

                    <!-- Committee Fields (Hidden by default) -->
                    <div id="committee_auth" style="display:none;">
                        <h5 class="text-danger mb-3"><i class="fas fa-lock"></i> كلمة السر</h5>
                        <div class="mb-3">
                            <label class="form-label required">أدخل كلمة السر الخاصة باللجنة</label>
                            <input type="password" name="committee_code" class="form-control" id="committee_code" onkeyup="checkCommitteeCode()">
                        </div>
                    </div>

                    <div id="committee_fields" style="display:none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> كلمة السر صحيحة!
                            <p class="mb-0 mt-1 small">ملاحظة: كعضو لجنة، ستقوم بتقييم المرشحين من 1 إلى 100. سيتم عرض متوسط التقييمات لرئيس اللجنة.</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">الدور في اللجنة</label>
                            <select name="committee_role" class="form-select">
                                <option value="committee">عضو لجنة</option>
                                <option value="chairman">رئيس لجنة</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">تسجيل الحساب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('reg_type').value;
        
        document.getElementById('central_fields').style.display = 'none';
        document.getElementById('general_fields').style.display = 'none';
        document.getElementById('committee_auth').style.display = 'none';
        document.getElementById('committee_fields').style.display = 'none';

        if (type === 'central') {
            document.getElementById('central_fields').style.display = 'block';
        } else if (type === 'general') {
            document.getElementById('general_fields').style.display = 'block';
        } else if (type === 'committee') {
            document.getElementById('committee_auth').style.display = 'block';
            // Check if code was already entered correctly
            checkCommitteeCode();
        }
    }

    function checkCommitteeCode() {
        const code = document.getElementById('committee_code').value;
        // Simple client-side check for UX, server-side validation is mandatory
        // Default code is 1232 as per user info
        if (code === '1232') {
            document.getElementById('committee_fields').style.display = 'block';
        } else {
            document.getElementById('committee_fields').style.display = 'none';
        }
    }
</script>
@endsection
