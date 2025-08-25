<div class="modal fade" id="idpUploadModal" tabindex="-1" aria-labelledby="idpUploadModalLabel" aria-hidden="true">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('idp.import.single') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                <div class="modal-header">
                    <h1 class="modal-title text-primary fw-bold fs-5" id="idpUploadModalLabel">Upload Development Plan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Donwload Template --}}
                    <div class="mb-3 border-bottom pb-3">
                        <label class="form-label">1. Download Template</label>
                        <div>
                            <a href="{{ route('idp.template.download', ['employeeId' => $employee->employee_id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-file-earmark-excel me-1"></i> Download Template Excel
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="idp_file" class="form-label">2. Pilih File Untuk Diunggah</label>
                        <input class="form-control" type="file" name="idp_file" id="idp_file" required accept=".xlsx, .xls">
                    </div>

                    <div class="alert alert-info mt-3" role="alert">
                        <h4 class="alert-heading h6">Pastikan format file Excel Anda sesuai:</h4>
                            <ul class="mb-0 small" style="padding-left: 1.2rem;">
                                <li>Please fill out your Individual Development Plan (IDP) in the "IDP" sheet.</li>
                                <li>The "Master" sheet is for reference only and contains the dropdown options for 'competency_name' and 'development_program'.</li>
                                <li>Ensure all data is entered according to the provided template.</li>
                                <li>The date format for "time_frame_start", "time_frame_end", and "realization_date" must be (DD-MM-YYYY). The dates entered cannot be after the current date.</li>
                                <li>If the "realization_date" has not yet occurred or is not applicable, please leave it blank, and it will be automatically filled with a dash(`-`).</li>
                            </ul>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>