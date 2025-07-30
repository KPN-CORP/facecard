@extends('layouts.app')
@section('title', 'Import Center')


@section('content')
<div class="container-fluid">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Import Center</h1>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i> Import New Data
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('import.index') }}" method="GET" id="filterHistoryForm">
    <div class="row mb-3 justify-content-between">
        <div class="col-md-auto d-flex align-items-center gap-2">
            
            {{-- Bagian "Show Entries" --}}
            <label for="per_page" class="form-label mb-0">Show</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span class="ms-1">entries</span>

            {{-- Tombol "Delete All" --}}
            @can('delete_all_import_logs')
                <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="deleteAllButton">
                    <i class="bi bi-trash-fill me-1"></i> Delete All History
                </button>
            @endcan
        </div>
        
        {{-- Bagian Search --}}
        <div class="col-md-4">
            <div class="input-group">
                <input type="search" name="search" class="form-control" placeholder="Search result or data type..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>

{{-- Form tersembunyi untuk proses delete all --}}
<form action="{{ route('import.destroy_all') }}" method="POST" id="deleteAllForm" style="display: none;">
    @csrf
    @method('DELETE')
</form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Data</th>
                            <th style="width: 20%;">Import Date</th>
                            <th>Result</th>
                            <th style="width: 120px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $log->data_type)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->import_date)->format('d F Y H:i') }}</td>
                                <td style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $log->result }}">
                                    @if($log->status === 'Success')
                                        <span class="text-success">{{ $log->result }}</span>
                                    @else
                                        <span class="text-danger">{{ $log->result }}</span>
                                    @endif
                                </td>
                                <td style="width: 120px; text-align: center;">
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
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No import history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {!! $logs->appends(request()->query())->links('vendor.pagination.custom') !!}
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
                            @can('import_talent_status') <option value="talent_status">Talent Status</option> @endcan
                            @can('import_proposed_grade') <option value="proposed_grade">Proposed Grade</option> @endcan
                        </select>
                    </div>

                    {{-- Instruction --}}
                    <div id="competency-assessment-instructions" class="alert alert-info mt-3" role="alert" style="display: none;">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai :</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Kolom 'employee_id' yang diawali '0' harus diberi tanda petik satu (contoh: `'012345`).</li>
                            <li>Format tanggal untuk 'assessment_date' adalah (YYYY-MM-DD).</li>
                            <li>Jika tidak ada nilai untuk competency score, isi dengan angka 0.</li>
                        </ul>
                    </div>

                     <div id="data-master-instructions" class="alert alert-info mt-3" role="alert" style="display: none;">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai :</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Kolom 'employee_id' yang diawali '0' harus diberi tanda petik satu (contoh: `'012345`).</li>
                            <li>Format 'period' adalah tahun (YYYY).</li>
                        </ul>
                    </div>

                     <div id="idp-instructions" class="alert alert-info mt-3" role="alert" style="display: none;">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai :</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Kolom 'employee_id' yang diawali '0' harus diberi tanda petik satu (contoh: `'012345`).</li>
                            <li>Format tanggal untuk 'assessment_date' adalah (YYYY-MM-DD).</li>
                            <li>Input Manual pada 'development_model' akan masuk ke Uncategorized Model</li>
                            <li>Jika tidak ada nilai untuk 'realization_date', isi dengan '-'.</li>
                        </ul>
                    </div>

                    <div id="talent-instructions" class="alert alert-info mt-3" role="alert" style="display: none;">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai :</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Kolom 'employee_id' yang diawali '0' harus diberi tanda petik satu (contoh: `'012345`).</li>
                            <li>Format 'period' adalah tahun (YYYY).</li>
                        </ul>
                    </div>

                    <div id="proposed-instructions" class="alert alert-info mt-3" role="alert" style="display: none;">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai :</h4>
                        <ul class="mb-0 small" style="padding-left: 1.2rem;">
                            <li>Kolom 'employee_id' yang diawali '0' harus diberi tanda petik satu (contoh: `'012345`).</li>
                            <li>Proposed Grade diisi dengan huruf kapital "(2A)"</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">2. Download Template</label>
                    <div>
                        {{-- Button styled to match the other modal --}}
                        <a href="#" id="downloadTemplateLink" class="btn btn-sm btn-outline-success" style="display: none;" download>
                            <i class="bi bi-file-earmark-excel me-1"></i> Download Template Excel
                         </a>
                        {{-- Help text remains the same --}}
                            <small id="templateHelpText" class="form-text text-muted">Pilih tipe data untuk melihat link download template.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="import_file" class="form-label">3. Pilih File untuk Diunggah *</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" required accept=".xlsx, .xls">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-upload me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importTypeSelect = document.getElementById('import_type');
    const downloadLink = document.getElementById('downloadTemplateLink');
    const helpText = document.getElementById('templateHelpText');
    const competencyInstructions = document.getElementById('competency-assessment-instructions');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const deleteAllForm = document.getElementById('deleteAllForm');

    if (deleteAllButton) {
            deleteAllButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Are you absolutely sure?',
                    text: "This action cannot be undone. All import logs and their associated files will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete everything!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteAllForm.submit();
                    }
                });
            });
        }

    const templates = {
        'competency_assessment': '{{ asset("templates/template_competency_assessment.xlsx") }}',
        'data_master': '{{ asset("templates/template_data_master.xlsx") }}',
        'idp': '{{ asset("templates/template_idp.xlsx") }}',
        'talent_box': '{{ asset("templates/template_talent_box.xlsx") }}',
        'talent_status': '{{ asset("templates/template_talent_status.xlsx") }}',
        'proposed_grade': '{{ asset("templates/template_proposed_grade.xlsx") }}',
    };
    
    if (importTypeSelect) {
    importTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Instuction initialization 
        const competencyInstructions = document.getElementById('competency-assessment-instructions');
        const dataMasterInstructions = document.getElementById('data-master-instructions');
        const idpInstructions = document.getElementById('idp-instructions');
        const talentInstructions = document.getElementById('talent-instructions');
        const proposedInstructions = document.getElementById('proposed-instructions');
        
        // Hide Instruction
        competencyInstructions.style.display = 'none';
        dataMasterInstructions.style.display = 'none';
        idpInstructions.style.display = 'none';
        talentInstructions.style.display = 'none';
        proposedInstructions.style.display = 'none';
        
        // Logic link download 
        if (selectedType && templates[selectedType]) {
            downloadLink.href = templates[selectedType];
            downloadLink.setAttribute('download', `template_${selectedType}.xlsx`);
            downloadLink.style.display = 'inline';
            if(helpText) helpText.style.display = 'none';
        } else {
            downloadLink.style.display = 'none';
            if(helpText) helpText.style.display = 'block';
        }

        // Show Instruction
        if (selectedType === 'competency_assessment') {
            competencyInstructions.style.display = 'block';
        } else if (selectedType === 'idp') {
            idpInstructions.style.display = 'block';
        } else if (selectedType === 'data_master') { // Untuk Matrix Grades
            dataMasterInstructions.style.display = 'block';
        } else if (selectedType === 'talent_box' || selectedType === 'talent_status') { // Untuk Talent Box & Status
            talentInstructions.style.display = 'block';
        } else if (selectedType === 'proposed_grade') {
            proposedInstructions.style.display = 'block';
        }
    });

    }
    const deleteForms = document.querySelectorAll('.form-delete-log');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! The log and its associated files will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
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