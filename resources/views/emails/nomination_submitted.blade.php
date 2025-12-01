<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #003366, #1A73E8);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; color: #333; }
        .info-box {
            background-color: #f8f9fa;
            border-right: 4px solid #003366;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
        .btn {
            display: inline-block;
            background-color: #FBBC04;
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 تم استلام ترشيحك بنجاح</h1>
        </div>
        <div class="content">
            <h2>مرحباً {{ $nomination->employee_name }}،</h2>
            <p>نشكرك على مشاركتك في نظام الترشيحات والتكريم بمصلحة الجمارك المصرية.</p>
            <p>تم استلام طلب الترشيح الخاص بك بنجاح وهو الآن قيد المراجعة.</p>
            
            <div class="info-box">
                <p><strong>رقم الترشيح:</strong> #{{ $nomination->id }}</p>
                <p><strong>الفئة:</strong> {{ $nomination->category }}</p>
                <p><strong>تاريخ التقديم:</strong> {{ $nomination->created_at->format('Y-m-d') }}</p>
            </div>

            <p>سيتم إعلامك بأي تحديثات جديدة عبر البريد الإلكتروني.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('nomination') }}" class="btn">زيارة الموقع</a>
            </div>
        </div>
        <div class="footer">
            <p>جميع الحقوق محفوظة © مصلحة الجمارك المصرية 2025</p>
        </div>
    </div>
</body>
</html>
