@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3 fw-bold text-success">تم التسجيل بنجاح!</h2>
                    <p class="lead mb-4 text-muted">شكراً لتسجيلك في نظام الترشيح والتقييم.</p>
                    
                    <div class="alert alert-info d-inline-block px-4 py-3 mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $successMessage }}
                    </div>

                    <div class="d-grid gap-2 col-md-6 mx-auto">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
