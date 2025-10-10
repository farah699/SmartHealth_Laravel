@extends('partials.layouts.master_auth')

@section('title', 'Sign In | SmartHealth')

@section('content')

    <!-- START -->
    <div>
        <img src="{{ asset('assets/images/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">Sign In to SmartHealth</h6>

                            <!-- Messages d'erreur et de succès -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <!-- Formulaire de connexion -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="Enter your email" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password"
                                               name="password"
                                               placeholder="Enter your password" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                           <div class="form-text">
    <a href="{{ route('forgot.password.form') }}"
       class="link link-primary text-muted text-decoration-underline">
       Forgot password?
    </a>
</div>

                                        </div>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Sign In<i
                                                class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                    </div>
                                </div>
                                
                                <!-- Google Sign In temporairement désactivé -->
                                <!--
                                <button type="button"
                                    class="mb-10 btn btn-outline-light w-full mb-4 d-flex align-items-center gap-2 justify-content-center text-muted">
                                    <img src="{{ asset('assets/images/google.png') }}" alt="Google Image" class="h-20px w-20px">
                                    Sign in with google
                                </button>
                                -->
                                
                                <p class="mb-0 fw-semibold position-relative text-center fs-12">Don't have an account? 
                                    <a href="{{ route('register') }}" class="text-decoration-underline text-primary">Sign up here</a>
                                </p>
                            </form>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">© 2025 SmartHealth. Crafted with ❤️</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
// Optionnel : JavaScript pour améliorer l'UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = 'Signing In... <i class="bi bi-hourglass-split ms-1 fs-16"></i>';
        submitBtn.disabled = true;
    });
});
</script>
@endsection