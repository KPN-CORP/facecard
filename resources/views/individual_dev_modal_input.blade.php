

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<div class="modal fade" id="idpModal" tabindex="-1" aria-labelledby="idpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="idpForm" action="{{ route('idp.store') }}" method="POST">
                @csrf
                <div id="idpFormMethod"></div>
                <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                
                <div class="modal-header">
                    <h1 class="modal-title" id="idpModalTitle">Input Development Plan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section bg-light p-3 rounded mb-4">
                        <h5>Development Area</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="idp_development_model_id" class="form-label">Development Model</label>
                                <select id="idp_development_model_id" name="development_model_id" class="form-select">
                                    <option value="">Uncategorized</option>
                                    @foreach($developmentModels as $model)
                                        <option value="{{ $model->id }}">{{ $model->name }} ({{ $model->percentage }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="idp_competency_type" class="form-label required-label">Competency Type</label>
                                <select id="idp_competency_type" name="competency_type" class="form-select" required>
                                    <option value="">Please select</option>
                                    <option value="Softskill">Soft Competency</option>
                                    <option value="Technical">Technical Competency</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="idp_competency_name" class="form-label required-label">Competency Name</label>
                                <select id="idp_competency_name" name="competency_name" placeholder="Select or type..." required>
                                    <option value="">Select or type...</option>
                                    @foreach($competencyNameOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="idp_review_tools" class="form-label required-label">Review Tools</label>
                                <select id="idp_review_tools" name="review_tools" placeholder="Select or type..." required>
                                    <option value="">Select or type...</option>
                                     @foreach($reviewToolOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section bg-light p-3 rounded mb-4">
                        <h5>Development Program</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idp_development_program" class="form-label required-label">Development Program</label>
                                <select id="idp_development_program" name="development_program" placeholder="Select or type..." required>
                                     <option value="">Select or type...</option>
                                     @foreach($developmentProgramOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="idp_expected_outcome" class="form-label required-label">Expected Outcome</label>
                                <input type="text" id="idp_expected_outcome" name="expected_outcome" class="form-control" placeholder="e.g., Fulfilling competency gaps" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section bg-light p-3 rounded">
                        <h5>Timeline & Result</h5>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="idp_time_frame_start" class="form-label required-label">Time Frame</label>
                                <div class="input-group">
                                    <input type="date" id="idp_time_frame_start" name="time_frame_start" class="form-control" required>
                                    <span class="input-group-text">→</span>
                                    <input type="date" id="idp_time_frame_end" name="time_frame_end" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="idp_realization_date" class="form-label">Realization Date</label>
                                <input type="date" id="idp_realization_date" name="realization_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="idp_result_evidence" class="form-label">Result Evidence</label>
                                <input type="text" id="idp_result_evidence" name="result_evidence" class="form-control" placeholder="e.g., Certificate Link">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-save">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tomSelectSettings = {
            create: true, 
            sortField: {
                field: "text",
                direction: "asc"
            }
        };
        new TomSelect("#idp_competency_name", tomSelectSettings);
        new TomSelect("#idp_development_program", tomSelectSettings);
        new TomSelect("#idp_review_tools", tomSelectSettings);
    const idpForm = document.getElementById('idpForm');
        if (idpForm) {
            idpForm.tomselect = tomSelectInstances;
        }
    });

    if (window.app && typeof app.openCreateIdpModal === 'function') {
        const originalOpenCreate = app.openCreateIdpModal;
        app.openCreateIdpModal = function() {
            originalOpenCreate(); 
            const form = document.getElementById('idpForm');
            if (form && form.tomselect) {
                for (const key in form.tomselect) {
                    form.tomselect[key].clear(); 
                }
            }
        };
    }
</script>
@endpush