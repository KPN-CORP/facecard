@push('navbar-right')
    @auth
        @php
            $employeeData = Auth::user()->employee;
        @endphp

        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                @if($employeeData && $employeeData->photo)
                    <img src="{{ asset($employeeData->photo) }}" class="rounded-circle" alt="User Photo" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.2rem;">
                        {{ substr($employeeData->fullname ?? Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow-1 ms-3">
                <span class="fw-bold d-block">{{ $employeeData->fullname ?? Auth::user()->name }}</span>
                @if($employeeData && $employeeData->employee_id)
                    <span class="text-muted d-block" style="font-size: 0.8em; line-height: 1;">{{ $employeeData->employee_id }}</span>
                @endif
            </div>
        </div>
    @endauth
@endpush



<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<aside class="sidebar">
    <div class="logo d-flex flex-column align-items-center mb-4">
        <img src="{{ asset('images/kpn-logo.png') }}" alt="KPN Corp Logo" style="height: 54px;">
        <h2 class="h6 mb-0 mt-2" style="font-weight: 600;">HC System</h2>
    </div>

    <p class="text-muted small text-uppercase fw-bold">MENU</p>
    <nav class="nav flex-column main-menu">
        
        {{-- Facecard --}}
        <a href="{{ route('facecard.list') }}" class="nav-link {{ request()->routeIs('facecard.list') || request()->routeIs('employee.profile') ? 'custom-active' : 'text-dark' }}">
            <i class="bi bi-person-badge me-2"></i> Facecard
        </a>

        {{-- IDP --}}
        @php
            $idpActive = request()->routeIs('idp.*') ? 'show' : '';
            $isIDPParentActive = request()->routeIs('idp.*');
            $isIDPListActive = request()->routeIs('idp.list') || request()->routeIs('idp.show');
        @endphp

        @can('view_idp_report')
        <div>
            <a href="#idpSubMenu" class="nav-link d-flex justify-content-between align-items-center {{ $isIDPParentActive ? 'custom-active' : 'text-dark' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isIDPParentActive ? 'true' : 'false' }}" aria-controls="idpSubMenu">
                <span><i class="bi bi-journal-check me-2"></i> IDP</span>
                <i class="bi {{ $isIDPParentActive ? 'bi-chevron-up' : 'bi-chevron-right' }} arrow-icon"></i>
            </a>
            <div class="collapse submenu {{ $idpActive }}" id="idpSubMenu">
                @can('view_idp_list')
                <a href="{{ route('idp.list') }}" class="nav-link text-dark {{ $isIDPListActive ? 'active' : '' }}">
                    IDP List
                </a>
                @endcan
                @can('view_idp_master')
                <a href="{{ route('idp.setting.index') }}" class="nav-link text-dark {{ request()->routeIs('idp.setting.index') ? 'active' : '' }}">
                    IDP Data Master
                </a>
                @endcan
            </div>
        </div>
        @endcan

        {{-- Report --}}
        @can('view_report_menu')
            <a href="{{ route('report.show') }}" class="nav-link {{ request()->routeIs('report.show') ? 'custom-active' : 'text-dark' }}">
                <i class="bi bi-file-earmark-text me-2"></i> Report
            </a>
        @endcan

        {{-- Import Center --}}
        @can('view_import_center')
            <a href="{{ route('import.index') }}" class="nav-link {{ request()->routeIs('import.index') ? 'custom-active' : 'text-dark' }}">
                <i class="bi bi-cloud-arrow-up me-2"></i> Import Center
            </a>
        @endcan

        {{-- Admin Setting --}}
        @can('view_admin_setting')
            @php
                $adminActive = request()->routeIs('roles.*') ? 'show' : '';
                $isAdminParentActive = request()->routeIs('roles.*');
                $isAdminSubActive = request()->routeIs('roles.index');
            @endphp

            <div>
                <a href="#adminSettingSubMenu" class="nav-link d-flex justify-content-between align-items-center {{ $isAdminParentActive ? 'custom-active' : 'text-dark' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isAdminParentActive ? 'true' : 'false' }}" aria-controls="adminSettingSubMenu">
                    <span><i class="bi bi-gear me-2"></i> Admin Setting</span>
                    <i class="bi {{ $isAdminParentActive ? 'bi-chevron-up' : 'bi-chevron-right' }} arrow-icon"></i>
                </a>
                <div class="collapse submenu {{ $adminActive }}" id="adminSettingSubMenu">
                    <a href="{{ route('roles.index') }}" class="nav-link text-dark {{ $isAdminSubActive ? 'active' : '' }}">
                        Role Setting
                    </a>
                </div>
            </div>
        @endcan

    </nav>

    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
        </form>
    </div>
</aside>
