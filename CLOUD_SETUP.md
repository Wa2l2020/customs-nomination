# إعداد التخزين السحابي (Cloud Storage Setup)

يدعم النظام الربط مع 3 خدمات تخزين سحابي لرفع المرفقات تلقائياً وإنشاء مجلدات خاصة لكل مرشح.

## المتطلبات العامة
لتفعيل أي خدمة، يجب تثبيت المكتبة الخاصة بها على السيرفر باستخدام `composer`.

### 1. Google Drive
**المكتبة:**
```bash
composer require masbug/flysystem-google-drive-ext
```
**الإعداد:**
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/).
2. أنشئ مشروعاً وفعل **Google Drive API**.
3. أنشئ **OAuth Client ID** واحصل على `Client ID` و `Client Secret`.
4. استخدم [OAuth Playground](https://developers.google.com/oauthplayground) للحصول على `Refresh Token`.
5. أدخل هذه البيانات في لوحة تحكم النظام.

### 2. Microsoft OneDrive
**المكتبة:**
```bash
composer require nicolasbeauvais/flysystem-onedrive
```
**الإعداد:**
1. اذهب إلى [Azure Portal](https://portal.azure.com/).
2. سجل تطبيقاً جديداً (App Registration).
3. احصل على `Client ID` (Application ID) و `Client Secret`.
4. تأكد من منح صلاحيات `Files.ReadWrite.All`.
5. أدخل البيانات في لوحة التحكم.

### 3. Dropbox
**المكتبة:**
```bash
composer require spatie/flysystem-dropbox
```
**الإعداد:**
1. اذهب إلى [Dropbox App Console](https://www.dropbox.com/developers/apps).
2. أنشئ تطبيقاً جديداً (Scoped Access).
3. احصل على `Access Token` (يمكن توليده مباشرة من لوحة التحكم الخاصة بالتطبيق).
4. أدخل التوكن في لوحة تحكم النظام.

## ملاحظات هامة
- **التلقائية**: النظام سيقوم تلقائياً بإنشاء مجلد باسم "رقم الحاسب" داخل المجلد الرئيسي للخدمة السحابية.
- **الاحتياطي**: في حال فشل الاتصال بالسحابة (انقطاع نت مثلاً)، سيقوم النظام بحفظ الملفات محلياً على السيرفر لضمان عدم ضياعها.
