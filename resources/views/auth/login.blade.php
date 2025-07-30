@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body, html {
        height: 100%;
        background-color: #f4f5f7;
    }
    .main-content {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1rem;
    }
    .login-card {
        display: flex;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: none;
        min-width: 800px; /* Ensures a minimum width on large screens */
    }
    .login-art {
        background-color: #fff;
        padding: 3rem 2rem; /* Increased padding */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-art-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        font-size: 3rem; /* Increased font size */
        font-weight: 300;
        letter-spacing: 0.75rem; /* Increased letter spacing */
        color: #e9ecef; /* Lighter color for better contrast */
        white-space: nowrap;
    }
    .login-form-section {
        background-color: #fff;
        padding: 3.5rem; /* Increased padding */
        width: 100%;
        max-width: 500px; /* Increased max-width */
    }
    .form-control-custom {
        border: none;
        border-bottom: 1px solid #ced4da;
        border-radius: 0;
        padding-left: 0;
    }
    .form-control-custom:focus {
        box-shadow: none;
        border-color: #c82333;
    }
    .input-group-text-custom {
        background: transparent;
        border: none;
        border-bottom: 1px solid #ced4da;
        border-radius: 0;
        font-size: 1.2rem;
        color: #adb5bd;
    }
    .btn-login {
        background-color: #c82333;
        border-color: #c82333;
        padding: 0.75rem;
        font-weight: 500;
    }
    .btn-login:hover {
        background-color: #a81d2a;
        border-color: #a81d2a;
    }
    /* Style for the password toggle button */
    .btn-toggle-password {
        border: none;
        border-bottom: 1px solid #ced4da;
        border-radius: 0;
        background-color: transparent;
        color: #adb5bd;
    }
    .btn-toggle-password:focus {
        box-shadow: none;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            {{-- Increased column width for a larger card --}}
            <div class="col-xl-9 col-lg-10">
                <div class="login-card">
                    {{-- Left side with vertical text --}}
                    <div class="login-art">
                        <h2 class="login-art-text">Login HC System</h2>
                    </div>

                    {{-- Right side with the login form --}}
                    <div class="login-form-section">
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="employee_id" class="form-label text-muted">Employee ID</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom pe-3">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input id="employee_id" type="text" class="form-control form-control-custom" name="employee_id" value="{{ old('employee_id') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label text-muted">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom pe-3">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input id="password" type="password" class="form-control form-control-custom" name="password" required>
                                    {{-- Show/Hide Password Button --}}
                                    <button class="btn btn-toggle-password" type="button" id="togglePassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-danger btn-login">Log In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the eye slash icon
        if (type === 'password') {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>
@endpush
