@extends('layouts.master')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-lg p-5 border-danger">
        <div class="card-body">
            <h1 class="display-1 text-danger mb-4"><i class="fas fa-clock"></i></h1>
            <h2 class="fw-bold text-danger mb-3">انتهت الفترة المحددة</h2>
            <p class="lead text-muted">{{ $message }}</p>
            <a href="/" class="btn btn-primary mt-4">العودة للرئيسية</a>
        </div>
    </div>
</div>
@endsection
