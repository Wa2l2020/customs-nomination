<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مرحباً بك في نظام الترشيح</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 20px; margin-bottom: 20px; }
        .content { font-size: 16px; line-height: 1.6; color: #333; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>مرحباً بك، {{ $user->name }}</h2>
        </div>
        <div class="content">
            <p>شكراً لتسجيلك في <strong>نظام الترشيح والتقييم - الجمارك المصرية</strong>.</p>
            <p>لقد تم إنشاء حسابك بنجاح بصلاحية: <strong>{{ $user->role_label }}</strong>.</p>
            <p>يمكنك الآن الدخول إلى النظام ومتابعة المهام الموكلة إليك.</p>
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">الدخول للنظام</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} الجمارك المصرية. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
