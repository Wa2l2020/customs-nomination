@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة المرفقات والأرشفة</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <!-- Total Size Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي حجم الملفات (محلي)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" dir="ltr">{{ $totalSizeMb }} MB</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Local Files Count Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">عدد الملفات المحلية</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $fileCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cloud Linked Count Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">المرشحين المرتبطين سحابياً</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $linkedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cloud fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Import CSV Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📥 استيراد الروابط (CSV)</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">قم برفع ملف CSV يحتوي على عمودين: <code>c_num</code> (رقم الحاسب) و <code>link</code> (الرابط) لتحديث الروابط بشكل جماعي.</p>
                    <form action="{{ route('admin.attachments.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="file" name="csv_file" class="form-control" accept=".csv, .txt" required>
                            <button class="btn btn-success" type="submit">استيراد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Archive Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">📦 أرشفة وتفريغ المساحة</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">تجميع كل الملفات المحلية في ملف مضغوط (ZIP) واحد منظم بمجلدات لكل مرشح.</p>
                    <form action="{{ route('admin.attachments.archive') }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="delete_after" id="deleteAfter">
                                <label class="form-check-label text-danger fw-bold small" for="deleteAfter">
                                    حذف الملفات الأصلية بعد التحميل (تفريغ مساحة)
                                </label>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-file-archive me-2"></i> تحميل الأرشيف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Nominations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المرشحين وإدارة الروابط</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%">رقم الحاسب</th>
                            <th style="width: 25%">الاسم</th>
                            <th style="width: 15%">الملفات المحلية</th>
                            <th style="width: 45%">رابط المجلد السحابي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nominations as $nom)
                        <tr>
                            <td class="align-middle">{{ $nom->job_number }}</td>
                            <td class="align-middle fw-bold">{{ $nom->employee_name }}</td>
                            <td class="align-middle text-center">
                                @if(!empty($nom->attachments) && count($nom->attachments) > 0)
                                    <span class="badge bg-success p-2">{{ count($nom->attachments) }} ملفات</span>
                                @else
                                    <span class="badge bg-secondary p-2">لا يوجد</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="link-{{ $nom->id }}" value="{{ $nom->cloud_folder_link }}" placeholder="https://drive.google.com/...">
                                    <button class="btn btn-primary btn-sm save-link" data-id="{{ $nom->id }}" title="حفظ الرابط">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    @if($nom->cloud_folder_link)
                                        <a href="{{ $nom->cloud_folder_link }}" target="_blank" class="btn btn-info btn-sm" title="فتح الرابط">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                                <small class="text-success d-none fw-bold mt-1" id="msg-{{ $nom->id }}">
                                    <i class="fas fa-check-circle me-1"></i> تم الحفظ بنجاح
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $nominations->links() }}
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.save-link');
        
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const input = document.getElementById('link-' + id);
                const msg = document.getElementById('msg-' + id);
                const link = input.value;

                // Simple validation
                if(link && !link.startsWith('http')) {
                    alert('الرابط يجب أن يبدأ بـ http أو https');
                    return;
                }

                // Show loading state
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;

                fetch(`/admin/attachments/${id}/update-link`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cloud_folder_link: link })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        msg.classList.remove('d-none');
                        setTimeout(() => msg.classList.add('d-none'), 3000);
                        input.classList.add('is-valid');
                        setTimeout(() => input.classList.remove('is-valid'), 3000);
                    } else {
                        alert('حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ في الاتصال');
                })
                .finally(() => {
                    this.innerHTML = originalIcon;
                    this.disabled = false;
                });
            });
        });
    });
</script>
@endsection
