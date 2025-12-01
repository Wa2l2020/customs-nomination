@extends('layouts.master')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
        </div>
        <h1 class="display-4 fw-bold text-success mb-3">تم إرسال الترشيح بنجاح!</h1>
        <p class="lead text-muted mb-5">شكراً لك، تم استلام طلبك وسيتم مراجعته قريباً.<br>تم إرسال رسالة تأكيد إلى بريدك الإلكتروني.</p>
        
        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
            <a href="{{ route('nomination') }}" class="btn btn-primary btn-lg px-5 gap-3">
                <i class="fas fa-plus-circle"></i> إدخال ترشيح آخر
            </a>
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-lg px-5">
                <i class="fas fa-sign-out-alt"></i> خروج
            </a>
        </div>
    </div>
</div>
@endsection
