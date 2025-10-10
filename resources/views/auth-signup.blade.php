@extends('partials.layouts.master_auth')

@section('title', 'Sign Up | SmartHealth')

@section('content')

<!-- START -->
<div>
    <img src="{{ asset('assets/images/auth/login_bg.jpg') }}" alt="Auth Background"
        class="auth-bg light w-full h-full auth-bg-cover opacity-60 position-absolute top-0">
    <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-10">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card mx-xxl-8">
                    <div class="card-body py-12 px-8">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Logo Dark" height="30"
                            class="mb-4 mx-auto d-block">
                        <h6 class="mb-3 mb-8 fw-medium text-center">Create Your Account in Minutes</h6>

                        <!-- Messages d'erreur -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label for="name" class="form-label">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Enter your username"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Email"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Select your role</option>
                                        <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                                        <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                    </select>
                                    @error('role')
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
                                           placeholder="Password"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirm Password" 
                                           required>
                                </div>
                                <div class="col-12 mt-8">
                                    <button type="submit" class="btn btn-primary w-full mb-4">Sign Up<i
                                            class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                </div>
                            </div>
                            
                            <p class="mb-0 fw-semibold position-relative text-center fs-12">Already have an account? <a
                                    href="{{ route('login') }}" class="text-decoration-underline text-primary">Sign In here</a>
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