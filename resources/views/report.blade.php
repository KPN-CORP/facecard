@extends('layouts.app')
@section('title', 'HC Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<div class="container-fluid">
    

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">...</div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert">...</div>@endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Report</h1>
            @if (auth()->user()->can('view_facecard_report') || auth()->user()->can('view_idp_report'))
            <div class="btn-group">
                <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Download Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('view_facecard_report')
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); app.downloadReport('talent_report');">Talent Status & Talent Box</a></li>
                    @endcan
                    @can('view_idp_report')
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); app.downloadReport('idp_progress');">IDP Progress</a></li>
                    @endcan
                </ul>
            </div>
            @endif
        </div>

        <div class="card-body">
            <form action="{{ route('report.show') }}" method="GET" id="filterReportForm">
                <div class="row g-2 align-items-end mb-3">
                    {{-- Show Entries --}}
                    <div class="col-auto">
                        <label for="per_page" class="form-label mb-1 small">Show</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit();">
                            <option value="10" @if(request('per_page', 10) == 10) selected @endif>10</option>
                            <option value="25" @if(request('per_page') == 25) selected @endif>25</option>
                            <option value="50" @if(request('per_page') == 50) selected @endif>50</option>
                            <option value="100" @if(request('per_page') == 100) selected @endif>100</option>
                        </select>
                    </div>
                    
                    {{-- Year Filter --}}
                    <div class="col-auto">
                        <label for="year" class="form-label mb-1 small">Year</label>
                        <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit();">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" @if($selectedYear == $year) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Business Unit Filter --}}
                    <div class="col">
                        <label for="business_unit" class="form-label mb-1 small">Business Unit</label>
                        <select name="business_unit" id="business_unit" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($filterOptions['businessUnits'] as $option)
                                <option value="{{ $option }}" @if(request('business_unit') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Job Level Filter --}}
                    <div class="col">
                        <label for="job_level" class="form-label mb-1 small">Job Level</label>
                        <select name="job_level" id="job_level" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['jobLevels'] as $option)
                                <option value="{{ $option }}" @if(request('job_level') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Designation Filter --}}
                    <div class="col">
                        <label for="designation" class="form-label mb-1 small">Designation</label>
                        <select name="designation" id="designation" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['designations'] as $option)
                                <option value="{{ $option }}" @if(request('designation') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Talent Status Filter --}}
                    <div class="col">
                        <label for="talent_status" class="form-label mb-1 small">Talent Status</label>
                        <select name="talent_status" id="talent_status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['talentStatuses'] as $option)
                                <option value="{{ $option }}" @if(request('talent_status') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Talent Box Filter --}}
                    <div class="col">
                        <label for="talent_box" class="form-label mb-1 small">Talent Box</label>
                        <select name="talent_box" id="talent_box" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($filterOptions['talentBoxes'] as $option)
                                <option value="{{ $option }}" @if(request('talent_box') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Box --}}
                    <div class="col-md-3">
                         <label for="search" class="form-label mb-1 small">Search</label>
                         <div class="input-group">
                             <input type="search" name="search" id="search" class="form-control form-select-sm" placeholder="Name or ID..." value="{{ request('search') }}">
                             <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                         </div>
                    </div>
                </div>
            </form>


            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th>No</th>
                            <th>Employee Name</th>
                            <th>Employee ID</th>
                            <th>Business Unit</th>
                            <th>Job Level</th>
                            <th>Designation</th>
                            @can('view_facecard_report')
                                <th>Talent Status</th>
                                <th>Talent Box</th>    
                            @endcan
                            @can('view_idp_report')
                                <th>IDP Progress</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                                <td>{{ $employee->fullname }}</td>
                                <td>{{ $employee->employee_id }}</td>
                                <td>{{ $employee->group_company ?? 'N/A' }}</td>
                                <td>{{ $employee->job_level ?? 'N/A' }}</td>
                                <td>{{ $employee->designation_name ?? 'N/A' }}</td>
                                @can('view_facecard_report')
                                    <td>{{ $employee->talent_status_for_year }}</td>
                                    <td>{{ $employee->talent_box_for_year }}</td>
                                @endcan
                                @can('view_idp_report')
                                    <td>{{ $employee->idp_progress }}</td>
                                @endcan
                            </tr>
                        @empty
                            @php
                                $colspan = 6;
                                if (auth()->user()->can('view_facecard_report')) $colspan += 2;
                                if (auth()->user()->can('view_idp_report')) $colspan += 1;
                            @endphp
                            <tr>
                                <td colspan="{{ $colspan }}" class="text-center py-4">No employee data found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {!! $employees->appends(request()->query())->links('vendor.pagination.custom') !!}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
if (typeof window.app === 'undefined') { window.app = {}; }

app.openModal = function(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
};

app.downloadReport = function(reportType) {
    // Take the main form
    const form = document.getElementById('filterReportForm');
    const originalAction = form.action;
    
    // Make hidden input for report name
    let reportNameInput = form.querySelector('input[name="report_name"]');
    if (!reportNameInput) {
        reportNameInput = document.createElement('input');
        reportNameInput.type = 'hidden';
        reportNameInput.name = 'report_name';
        form.appendChild(reportNameInput);
    }
    
    // Set new action and value for download
    reportNameInput.value = reportType;
    form.action = "{{ route('report.download') }}";
    form.method = "POST"; 

    // Add CSRF token if it doesnt exist
    let csrfInput = form.querySelector('input[name="_token"]');
    if (!csrfInput) {
        csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
    }

    // Submit download form
    form.submit();
    
    form.action = originalAction;
    form.method = "GET";
    reportNameInput.remove();
    csrfInput.remove();
};


</script>
@endpush
