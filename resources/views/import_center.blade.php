@extends('layouts.app')
@section('title', 'Import Center')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-primary">Import Center</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i> Import New Data
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('import.index') }}" method="GET" id="filterHistoryForm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div class="d-flex align-items-center gap-2">
                        

                        @can('delete_all_import_logs')
                            <button type="button" class="btn btn-outline-primary btn-sm ms-2" id="deleteAllButton">
                                <i class="bi bi-trash-fill me-1"></i> Delete All History
                            </button>
                        @endcan
                    </div>
                </div>
            </form>

            <form action="{{ route('import.destroy_all') }}" method="POST" id="deleteAllForm" class="d-none">
                @csrf
                @method('DELETE')
            </form>

            <div class="table-responsive">
                <table id="importLogsTable" class="table table-hover align-middle" style="width:100%">
                    <thead class="table-secondary">
                        <tr class="text-center">
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Data</th>
                            <th style="width: 20%;">Import Date</th>
                            <th>Result</th>
                            <th class="no-sort" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $log->data_type)) }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($log->import_date)->format('d F Y H:i') }}</td>
                                <td class="text-truncate" style="max-width: 300px;" title="{{ $log->result }}">
                                    @if($log->status === 'Success')
                                        <span class="text-success">{{ $log->result }}</span>
                                    @else
                                        <span class="text-danger">{{ $log->result }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('import.download', $log->id) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('import.destroy', $log->id) }}" method="POST" class="d-inline form-delete-log">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{{-- MODAL for Import Form --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="importModalLabel">Import New Data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_type" class="form-label">1. Pilih Tipe Data untuk Diimpor *</label>
                        <select name="import_type" id="import_type" class="form-select" required>
                            <option value="">Please select...</option>
                            @can('import_competency_assessment') <option value="competency_assessment">Competency Assessment</option> @endcan
                            @can('import_data_master') <option value="data_master">Data Master (Matrix Grades)</option> @endcan
                            @can('import_idp') <option value="idp">Individual Development Program</option> @endcan
                            @can('import_talent_box') <option value="talent_box">Talent Box</option> @endcan
                            @can('import_potential') <option value="potential">Potential</option> @endcan
                            @can('import_proposed_grade') <option value="proposed_grade">Proposed Grade</option> @endcan
                        </select>
                    </div>

                    {{-- Instruction --}}
                    <div id="competency-assessment-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>For 'employee_id' columns that start with '0', a single quote must be added before the number (example: `'012345`).</li>
                            <li>The date format for 'assessment_date' is date.</li>
                            <li>If there is no value for the competency score, please fill it with the number 0.</li>
                        </ul>
                    </div>

                     <div id="data-master-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>The format for 'period' is the year (YYYY).</li>
                            <li>'matrix_grade' must be filled with capital letters "(2A)".</li>
                        </ul>
                    </div>

                     <div id="idp-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Please fill out your Individual Development Plan (IDP) in the "IDP" sheet.</li>
                            <li>The "Master" sheet is for reference only and contains the dropdown options for 'competency_name' and 'development_program'.</li>
                            <li>Ensure all data is entered according to the provided template.</li>
                            <li>For 'employee_id' columns that start with '0', a single quote must be added before the number (example: `'012345`).</li>
                            <li>The 'assessment_date' must use the Date format in Excel.</li>
                            <li>If there is no value for 'realization_date', please fill it with '-'.</li>
                        </ul>
                    </div>

                    <div id="talent-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>For 'employee_id' columns that start with '0', a single quote must be added before the number (example: `'012345`).</li>
                            <li>The format for 'period' is the year (YYYY).</li>
                        </ul>
                    </div>

                    <div id="proposed-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>For 'employee_id' columns that start with '0', a single quote must be added before the number (example: `'012345`).</li>
                            <li>The Proposed Grade must be filled with capital letters "(2A)".</li>
                        </ul>
                    </div>

                    <div id="internal-instructions" class="alert alert-info mt-3 d-none" role="alert">
                        <h4 class="alert-heading h6">Please ensure your Excel file format is correct:</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>For 'employee_id' columns that start with '0', a single quote must be added before the number (example: `'012345`).</li>
                            <li>The 'from_date' and 'to_date' must use the Date format in Excel.</li>
                            <li>The Job Grade must be filled with capital letters "(2A)".</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">2. Download Template</label>
                        <div>
                            <a href="#" id="downloadTemplateLink" class="btn btn-sm btn-outline-success d-none" download>
                                <i class="bi bi-file-earmark-excel me-1"></i> Download Template Excel
                            </a>
                            <small id="templateHelpText" class="form-text text-muted">Select a data type to see the template link.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="import_file" class="form-label">3. Choose File to Upload <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" id="import_file" class="form-control" required accept=".xlsx, .xls">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- jQuery (DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
    $('#importLogsTable').DataTable({
        "lengthMenu": [ [10, 25, 50], [10, 25, 50] ],
        "pageLength": 10,
        "order": [[ 2, "desc" ]], 
        "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false
        } ]
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const importTypeSelect = document.getElementById('import_type');
    const downloadLink = document.getElementById('downloadTemplateLink');
    const helpText = document.getElementById('templateHelpText');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const deleteAllForm = document.getElementById('deleteAllForm');

    // Mengambil semua elemen instruksi
    const instructionBlocks = {
        'competency_assessment': document.getElementById('competency-assessment-instructions'),
        'data_master': document.getElementById('data-master-instructions'),
        'idp': document.getElementById('idp-instructions'),
        'talent_box': document.getElementById('talent-instructions'),
        'potential': document.getElementById('talent-instructions'), 
        'proposed_grade': document.getElementById('proposed-instructions'),
        'internal_movement': document.getElementById('internal-instructions')
    };

    const templates = {
        'competency_assessment': '{{ asset("templates/template_competency_assessment.xlsx") }}',
        'data_master': '{{ asset("templates/template_data_master.xlsx") }}',
        'idp': '{{ asset("templates/template_idp.xlsx") }}',
        'talent_box': '{{ asset("templates/template_talent_box.xlsx") }}',
        'potential': '{{ asset("templates/template_potential.xlsx") }}',
        'proposed_grade': '{{ asset("templates/template_proposed_grade.xlsx") }}',
    };

    if (importTypeSelect) {
        importTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;

            // Hide the instruction first
            Object.values(instructionBlocks).forEach(block => {
                if (block) block.classList.add('d-none');
            });

            // Logic to show link
            if (selectedType && templates[selectedType]) {
                downloadLink.href = templates[selectedType];
                downloadLink.setAttribute('download', `template_${selectedType}.xlsx`);
                downloadLink.classList.remove('d-none');
                if(helpText) helpText.classList.add('d-none');
            } else {
                // ClassList for show/hide
                downloadLink.classList.add('d-none');
                if(helpText) helpText.classList.remove('d-none');
            }

            const instructionToShow = instructionBlocks[selectedType];
            if (instructionToShow) {
                instructionToShow.classList.remove('d-none');
            }
        }); 
    }
    
    if (deleteAllButton) {
        deleteAllButton.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you absolutely sure?',
                text: "This action cannot be undone. All import logs will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#AB2F2B',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete everything!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAllForm.submit();
                }
            });
        });
    }
    
    const deleteForms = document.querySelectorAll('.form-delete-log');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#AB2F2B',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    });

});
</script>
@endpush