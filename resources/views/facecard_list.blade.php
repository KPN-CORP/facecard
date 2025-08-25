@extends('layouts.app')
@section('title', $pageTitle ?? 'Employee List')

@push('styles')
{{-- CSS untuk DataTables dengan tema Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

@endpush

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 fw-bold text-primary">{{ $pageTitle ?? 'Employee List' }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="employeesTable" class="table table-hover small align-middle" style="width:100%">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center align-middle">No</th>
                            <th class="text-center align-middle">Employee ID</th>
                            <th class="text-center align-middle">Employee Name</th>
                            <th class="text-center align-middle">Business Unit</th>
                            <th class="text-center align-middle">Job Grade</th>
                            <th class="text-center align-middle">Designation</th>
                            <th  class="text-center align-middle">Action</th> 
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $employee->employee_id }}</td>
                                <td>{{ $employee->fullname }}</td>
                                <td>{{ $employee->group_company ?? 'N/A' }}</td>
                                <td>{{ $employee->job_level ?? 'N/A' }}</td>
                                <td>{{ $employee->designation_name ?? $employee->designation ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if(request()->routeIs('idp.list'))
                                        <a href="{{ route('idp.show', $employee->employee_id) }}" class="btn btn-outline-primary btn-sm" title="Manage IDP">
                                            <i class="bi bi-journal-check"></i> Manage IDP
                                        </a>
                                    @else
                                        <a href="{{ route('employee.profile', ['employeeId' => $employee->employee_id]) }}" class="btn btn-outline-primary btn-sm" title="View Facecard">
                                            View Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

{{-- Script untuk mengaktifkan DataTables --}}
<script>
$(document).ready(function() {
    $('#employeesTable').DataTable({
        // Opsi untuk mengatur jumlah entri per halaman
        "lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ],
        // Jumlah entri default yang ditampilkan
        "pageLength": 10,
        // Menonaktifkan sorting pada kolom yang memiliki kelas 'no-sort'
        "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false
        } ]
    });
});
</script>
@endpush