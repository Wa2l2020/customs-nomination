<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_title'] ?? 'نظام الترشيحات' }}</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, {{ $settings['theme_color'] ?? '#003366' }} 0%, #0056b3 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 15px 25px;
            transition: all 0.3s;
        }
        .nav-tabs .nav-link.active {
            color: {{ $settings['theme_color'] ?? '#003366' }};
            border-bottom: 3px solid {{ $settings['theme_color'] ?? '#003366' }};
            background: transparent;
        }
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: {{ $settings['theme_color'] ?? '#003366' }};
        }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            padding: 40px;
            min-height: 400px;
        }
        .action-btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                @if(isset($settings['logo_url']) && $settings['logo_url'])
                    <img src="{{ $settings['logo_url'] }}" alt="Logo" height="40" class="d-inline-block align-text-top me-2">
                @endif
                {{ $settings['site_title'] ?? 'نظام الترشيحات' }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-primary" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white px-4 ms-2 rounded-pill" href="{{ route('register') }}">إنشاء حساب</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">{{ $settings['nomination_page_title'] ?? 'مرحباً بكم في نظام الترشيحات' }}</h1>
            <p class="lead opacity-75">{{ $settings['nomination_page_subtitle'] ?? 'منصة تكريم المتميزين - مصلحة الجمارك المصرية' }}</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mb-5">
        
        <!-- Tabs -->
        <ul class="nav nav-tabs justify-content-center mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">
                    <i class="fas fa-home me-2"></i> الرئيسية
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="instructions-tab" data-bs-toggle="tab" data-bs-target="#instructions" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i> الإرشادات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">
                    <i class="fas fa-users me-2"></i> عن الفريق
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            
            <!-- Home Tab -->
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <div class="content-card text-center">
                    <div class="row justify-content-center align-items-center h-100">
                        <div class="col-md-8">
                            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" alt="Nomination" width="120" class="mb-4 opacity-75">
                            <h3 class="fw-bold mb-4">ابدأ رحلة التميز الآن</h3>
                            <p class="text-muted mb-5">
                                يمكنك الآن تقديم طلبات الترشح، متابعة الحالة، والتواصل مع الإدارة من خلال منصتنا الإلكترونية الموحدة.
                            </p>
                            
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <button onclick="document.getElementById('instructions-tab').click()" class="btn btn-info text-white action-btn shadow">
                                    <i class="fas fa-info-circle me-2"></i> الإرشادات
                                </button>
                                <a href="{{ route('nomination') }}" class="btn btn-success action-btn shadow">
                                    <i class="fas fa-file-signature me-2"></i> تقديم طلب ترشيح
                                </a>
                                @guest
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary action-btn">
                                        <i class="fas fa-user-plus me-2"></i> تسجيل حساب جديد
                                    </a>
                                @endguest
                            </div>

                            <!-- Nomination Inquiry Section -->
                            <div class="mt-5 pt-4 border-top">
                                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-search me-2"></i>استعلام عن حالة طلب</h5>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <form id="inquiryForm" onsubmit="event.preventDefault(); checkStatus();">
                                            <div class="input-group mb-3">
                                                <input type="text" id="job_number_search" class="form-control form-control-lg text-center" placeholder="أدخل رقم الحاسب (الوظيفي)" required>
                                                <button class="btn btn-primary" type="submit">بحث</button>
                                            </div>
                                        </form>
                                        <div id="inquiryResult" class="mt-3" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions Tab -->
            <div class="tab-pane fade" id="instructions" role="tabpanel">
                <div class="content-card">
                    <h4 class="fw-bold border-bottom pb-3 mb-4 text-primary fs-5 fs-md-4">📋 إرشادات وقواعد الترشيح</h4>
                    <div class="lh-lg text-secondary">
                        @if(!empty($settings['instructions_content']))
                            {!! $settings['instructions_content'] !!}
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p>لا توجد إرشادات مضافة حالياً.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- About Team Tab -->
            <div class="tab-pane fade" id="about" role="tabpanel">
                <div class="content-card">
                    <h4 class="fw-bold border-bottom pb-3 mb-4 text-primary">👥 عن فريق العمل</h4>
                    <div class="lh-lg text-secondary">
                        @if(!empty($settings['about_team_content']))
                            {!! $settings['about_team_content'] !!}
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                                <p>لا توجد معلومات مضافة حالياً.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted mt-auto border-top bg-white">
        <div class="container">
            <small>&copy; {{ date('Y') }} {{ $settings['site_title'] ?? 'نظام الترشيحات' }}. جميع الحقوق محفوظة.</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'instructions') {
                const tabTrigger = new bootstrap.Tab(document.getElementById('instructions-tab'));
                tabTrigger.show();
            }
        });

        function checkStatus() {
            const jobNumber = document.getElementById('job_number_search').value;
            const resultDiv = document.getElementById('inquiryResult');
            
            if (!jobNumber) return;

            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            fetch('{{ route("nomination.inquiry") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ job_number: jobNumber })
            })
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    if (data.status === 'winner') {
                        // Celebratory Display for Winners
                        resultDiv.innerHTML = `
                            <div class="text-center p-5 position-relative overflow-hidden" style="
                                border: 4px solid #FFD700; 
                                border-radius: 15px; 
                                box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
                                background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)), url('https://usagif.com/wp-content/uploads/gif/confetti-4.gif');
                                background-size: cover;
                                background-position: center;">
                                
                                <div class="position-relative" style="z-index: 2;">
                                    <i class="fas fa-trophy fa-4x text-warning mb-3 animate__animated animate__bounceIn"></i>
                                    <h2 class="fw-bold text-dark mb-3" style="font-family: 'Cairo', sans-serif; color: #B8860B !important;">🎉 ألف مبروك! 🎉</h2>
                                    
                                    <h4 class="mb-4 text-dark">يسعدنا إبلاغكم بفوزكم بجائزة</h4>
                                    <h3 class="badge bg-warning text-dark fs-4 mb-4 px-4 py-2 shadow-sm">${data.category}</h3>
                                    
                                    <div class="mt-3">
                                        <h5 class="fw-bold text-dark">${data.name}</h5>
                                        <p class="text-muted">تاريخ التقديم: ${data.date}</p>
                                    </div>

                                    <div class="mt-4">
                                        <p class="lead text-dark fw-bold">"التميز هو نتيجة سعي دائم نحو الأفضل"</p>
                                        <small class="text-muted">مع تحيات مصلحة الجمارك المصرية</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Standard Display for other statuses
                        let statusBadge = '';
                        switch(data.status) {
                            case 'pending': statusBadge = '<span class="badge bg-secondary">قيد الانتظار</span>'; break;
                            case 'approved_general': statusBadge = '<span class="badge bg-info text-dark">تم اعتماد المدير العام</span>'; break;
                            case 'approved_central': statusBadge = '<span class="badge bg-primary">تم اعتماد الإدارة المركزية</span>'; break;
                            case 'committee_review': statusBadge = '<span class="badge bg-warning text-dark">مرحلة التحكيم</span>'; break;
                            case 'rejected': statusBadge = '<span class="badge bg-danger">مرفوض</span>'; break;
                            default: statusBadge = '<span class="badge bg-light text-dark">' + data.status + '</span>';
                        }

                        resultDiv.innerHTML = `
                            <div class="alert alert-success text-start">
                                <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>تم العثور على الطلب</h6>
                                <hr>
                                <p class="mb-1"><strong>الاسم:</strong> ${data.name}</p>
                                <p class="mb-1"><strong>الفئة:</strong> ${data.category}</p>
                                <p class="mb-1"><strong>الحالة الحالية:</strong> ${statusBadge}</p>
                                <p class="mb-0 small text-muted">تاريخ التقديم: ${data.date}</p>
                            </div>
                        `;
                    }
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> لا يوجد طلب مسجل بهذا الرقم الوظيفي.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i> حدث خطأ أثناء البحث. حاول مرة أخرى.
                    </div>
                `;
            });
        }
    </script>
</body>
</html>
