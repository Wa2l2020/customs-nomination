# إعداد Google Drive API

لربط الموقع مباشرة مع Google Drive (بدون استخدام Google Drive for Desktop)، يجب اتباع الخطوات التالية:

## 1. تثبيت المكتبة المطلوبة
افتح موجه الأوامر (Terminal) في مجلد المشروع ونفذ الأمر التالي:
```bash
composer require masbug/flysystem-google-drive-ext
```

## 2. إعداد Google Cloud Console
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/).
2. أنشئ مشروعاً جديداً.
3. فعل **Google Drive API** من المكتبة (Library).
4. اذهب إلى **Credentials** وأنشئ **OAuth Client ID**.
5. احصل على `Client ID` و `Client Secret`.
6. احصل على `Refresh Token` (يمكنك استخدام [Google OAuth Playground](https://developers.google.com/oauthplayground)).

## 3. إعداد ملف .env
افتح ملف `.env` في مجلد المشروع وأضف البيانات التالية:
```env
GOOGLE_DRIVE_CLIENT_ID=your-client-id
GOOGLE_DRIVE_CLIENT_SECRET=your-client-secret
GOOGLE_DRIVE_REFRESH_TOKEN=your-refresh-token
GOOGLE_DRIVE_FOLDER_ID=your-folder-id-where-files-go
```

## 4. تفعيل الإعداد في الموقع
1. اذهب إلى لوحة تحكم المدير.
2. في قسم الإعدادات، اختر **نوع التخزين: Google Drive API**.
3. احفظ الإعدادات.

الآن سيقوم الموقع برفع الملفات مباشرة إلى جوجل درايف.
