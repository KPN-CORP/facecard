@extends('layouts.app')
@section('title', 'HC Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-primary">Report</h1>
            @if (auth()->user()->can('download_talent') || auth()->user()->can('download_idp_progress'))
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Download Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('download_talent')
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); app.downloadReport('talent_report');">Potential & Talent Box</a></li>
                    @endcan
                    @can('download_idp_progress')
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); app.downloadReport('idp_progress');">IDP Progress</a></li>
                    @endcan
                </ul>
            </div>
            @endif
        </div>

        <div class="card-body">
            <form action="{{ route('report.show') }}" method="GET" id="filterReportForm">
                <div class="row g-2 align-items-end mb-4">
                    <div class="col-auto">
                        <label for="year" class="form-label mb-1 small text-muted">Year</label>
                        <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit();">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" @if($selectedYear == $year) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="business_unit" class="form-label mb-1 small text-muted">Business Unit</label>
                        <select name="business_unit" id="business_unit" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($filterOptions['businessUnits'] as $option)
                                <option value="{{ $option }}" @if(request('business_unit') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="job_level" class="form-label mb-1 small text-muted">Job Level</label>
                        <select name="job_level" id="job_level" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['jobLevels'] as $option)
                                <option value="{{ $option }}" @if(request('job_level') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="designation" class="form-label mb-1 small text-muted">Designation</label>
                        <select name="designation" id="designation" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['designations'] as $option)
                                <option value="{{ $option }}" @if(request('designation') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
        <label for="unit" class="form-label mb-1 small text-muted">Unit</label>
        <select name="unit" id="unit" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach($filterOptions['units'] as $option)
                <option value="{{ $option }}" @if(request('unit') == $option) selected @endif>{{ $option }}</option>
            @endforeach
        </select>
    </div>
                    <div class="col">
                        <label for="potential" class="form-label mb-1 small text-muted">Talent Status</label>
                        <select name="potential" id="potential" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                             @foreach($filterOptions['talentStatuses'] as $option)
                                <option value="{{ $option }}" @if(request('potential') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="talent_box" class="form-label mb-1 small text-muted">Talent Box</label>
                        <select name="talent_box" id="talent_box" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($filterOptions['talentBoxes'] as $option)
                                <option value="{{ $option }}" @if(request('talent_box') == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                 <table id="reportTable" class="table table-hover small align-middle" style="width:100%">
                    <thead class="table-secondary">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Business Unit</th>
                            <th>Job Level</th>
                            <th>Designation</th>
                            <th>Unit</th>
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
                        @foreach($employees as $employee)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $employee->employee_id }}</td>
                                <td>{{ $employee->fullname }}</td>
                                <td>{{ $employee->group_company ?? 'N/A' }}</td>
                                <td>{{ $employee->job_level ?? 'N/A' }}</td>
                                <td>{{ $employee->designation_name ?? 'N/A' }}</td>
                                <td>{{ $employee->unit ?? 'N/A' }}</td>
                                @can('view_facecard_report')
                                    <td>{{ $employee->potential_for_year }}</td>
                                    <td>{{ $employee->talent_box_for_year }}</td>
                                @endcan
                                @can('view_idp_report')
                                    <td>{{ $employee->idp_progress }}</td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Laravel dihapus --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery (diperlukan oleh DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- JavaScript untuk DataTables dan integrasi Bootstrap 5 --}}
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
    $(document).ready(function() {
    $('#reportTable').DataTable({
        "lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ],
        "pageLength": 10
    });
});
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
