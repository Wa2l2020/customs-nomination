<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_title'] ?? 'نظام الترشيحات والتكريم' }}</title>
    <link rel="icon" href="{{ $settings['logo_url'] ?? 'https://customs.gov.eg/images/EcaLogoLarge.png' }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: {{ $settings['theme_color'] ?? '#003366' }};
            --accent-color: #1A73E8;
            --gold-color: #FBBC04;
            --bg-color: #f5f7fa;
            --text-color: #333;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            height: 80px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .site-title h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .site-title p {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
        }

        /* Navbar */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 10px 0;
        }

        .nav-link {
            color: var(--primary-color) !important;
            font-weight: 600;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white !important;
            border-radius: 20px;
            padding: 8px 20px;
        }

        .btn-login:hover {
            background-color: var(--accent-color);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            padding: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Footer */
        .main-footer {
            background-color: white;
            border-top: 1px solid #eee;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: auto;
        }

        /* Utilities */
        .text-primary-custom { color: var(--primary-color); }
        .bg-primary-custom { background-color: var(--primary-color); }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        /* Form Elements */
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header -->
    <header class="main-header d-print-none">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <img src="{{ $settings['logo_url'] ?? 'https://customs.gov.eg/images/EcaLogoLarge.png' }}" alt="Logo" class="logo-img">
                    <div class="site-title">
                        <h1>{{ $settings['site_title'] ?? 'نظام الترشيحات والتكريم' }}</h1>
                        <p>مصلحة الجمارك المصرية - 2025</p>
                    </div>
                </div>
                @if(isset($settings['support_url']))
                <a href="{{ $settings['support_url'] }}" target="_blank" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="fas fa-headset"></i> الدعم الفني
                </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top d-print-none">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">📊 لوحة المعلومات</a></li>
                        @if(Auth::user()->role == 'admin')
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">🛠️ إدارة النظام</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="{{ route('nomination') }}">📝 ترشيح جديد</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('nomination') }}">📝 استمارة الترشيح (عام)</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/?tab=instructions') }}">📋 الإرشادات</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">⚙️ تسجيل المديرين</a></li>
                    @endauth
                </ul>
                <div class="d-flex">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('logout') }}">تسجيل الخروج</a></li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login">
                            <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="main-content">


        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="main-footer d-print-none">
        <div class="container">
            <p class="mb-1">جميع الحقوق محفوظة © مصلحة الجمارك المصرية 2025</p>
            <small class="text-muted">
                لأي مشكلة أو استفسار <a href="{{ $settings['support_url'] ?? '#' }}" target="_blank">اضغط هنا</a>
            </small>
        </div>
    </footer>

    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif
        
        @if($errors->any())
            let errorHtml = '<ul class="text-start" style="direction: rtl;">';
            @foreach ($errors->all() as $error)
                errorHtml += '<li>{{ $error }}</li>';
            @endforeach
            errorHtml += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'يرجى مراجعة الأخطاء التالية',
                html: errorHtml,
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33',
                width: '600px'
            });
        @endif
    </script>
    @yield('scripts')
</body>
</html>
