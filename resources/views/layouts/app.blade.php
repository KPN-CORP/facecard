<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HC System')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Styles pushed from individual pages --}}
    @stack('styles')

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #fff;
            --main-bg: #f8f9fa;
            --bs-primary-rgb: 171, 47, 43;
            --bs-primary: #AB2F2B;
            --bs-link-color-rgb: 171, 47, 43;
        }
        body {
            background-color: var(--main-bg);
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            border-right: 1px solid #dee2e6;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease-in-out;
            z-index: 1030; /* Ensure sidebar is above content */
        }
        .main-content-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease-in-out;
        }
        /* State when sidebar is hidden */
        body.sidebar-hidden .sidebar {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }
        body.sidebar-hidden .main-content-wrapper {
            margin-left: 0;
            width: 100%;
        }
        /* Custom Navbar */
        .navbar-custom {
            position: sticky;
            top: 0;
            z-index: 1020;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            padding: .75rem 1.5rem;
        }
        .menu-icon {
            font-size: 1.5rem;
            cursor: pointer;
            color: #495057;
        }

        /* --- Desktop-Only View Logic --- */
        .desktop-only-message {
            display: none; /* Hidden by default on large screens */
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100vh;
            padding: 2rem;
        }
        .desktop-only-message .bi-laptop {
            font-size: 8rem;
            color: #dd2424;
            margin-bottom: 1.5rem;
        }

        /* Show the desktop-only message */
        @media (max-width: 991.98px) {
            .page-wrapper {
                display: none !important;
            }
            .desktop-only-message {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="page-wrapper">
            @auth
                @include('layouts.partials.sidebar')
            @endauth

            <div class="main-content-wrapper">
                @auth
                    <nav class="navbar-custom d-flex justify-content-between">
                        <i class="bi bi-list menu-icon" id="menu-toggle-button"></i>
                        {{-- Slot for right-side navbar content --}}
                        @stack('navbar-right')
                    </nav>
                @endauth
                
                <main class="main-content p-4">
                    @yield('content')
                </main>
            </div>
        </div>

        {{-- This div is only visible on mobile devices --}}
        <div class="desktop-only-message">
            <i class="bi bi-laptop"></i>
            <h3>Desktop Only</h3>
            <p>This application is designed for a larger screen. Please switch to a desktop or laptop for the best experience.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- ** 1. TAMBAHKAN SWEETALERT2 LIBRARY DI SINI ** --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script global Anda (sudah ada) --}}
<script>
    if (typeof window.app === 'undefined') { window.app = {}; }

    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('menu-toggle-button');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-hidden');
            });
        }
    });
    

    // ** 2. TAMBAHKAN SCRIPT UNTUK NOTIFIKASI OTOMATIS DI SINI **
    // Script ini akan menangkap session 'success' atau 'error' dari Controller
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end', // Muncul di pojok kanan atas
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3500, // Hilang setelah 3.5 detik
            timerProgressBar: true
        });
    @endif
    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 5000, // Error ditampilkan sedikit lebih lama
            timerProgressBar: true
        });
    @endif
</script>

@stack('scripts')

</body>
</html>

