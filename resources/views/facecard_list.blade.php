@extends('layouts.app')
@section('title', $pageTitle ?? 'Employee List')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h1 class="h4 mb-0" style="font-weight: 600;">{{ $pageTitle ?? 'Employee List' }}</h1>
        </div>
        <div class="card-body">
            <form action="{{ request()->url() }}" method="GET" id="filterForm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <label for="per_page" class="form-label me-2 mb-0">Show</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="ms-2 text-muted">entries</span>
                    </div>
                    
                    {{-- Search input --}}
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Search by name, ID, or designation..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
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
                            <th class="text-center">Action</th>
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
                                <td>{{ $employee->designation_name ?? $employee->designation ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if(request()->routeIs('idp.list'))
                                        <a href="{{ route('idp.show', $employee->employee_id) }}" class="btn btn-danger btn-sm" title="Manage IDP">
                                            <i class="bi bi-journal-check"></i> Manage IDP
                                        </a>
                                    @else
                                        <a href="{{ route('employee.profile', ['employeeId' => $employee->employee_id]) }}" class="btn btn-danger btn-sm" title="View Facecard">
                                            View Profile
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    @if(request('search'))
                                        No employees found matching your search for "{{ request('search') }}".
                                    @else
                                        No employee data found.
                                    @endif
                                </td>
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
