

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
                    <h1 class="modal-title text-primary fw-bold" id="idpModalTitle">Input Development Plan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
    <div class="p-3 rounded mb-3" style="background-color: #f8f9fa;">
        <h5 class="fw-bold text-primary">Development Area</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label for="idp_development_model_id" class="form-label">Development Model</label>
                <select id="idp_development_model_id" name="development_model_id" class="form-select">
                    @foreach($developmentModels as $model)
                        <option value="{{ $model->id }}">{{ $model->name }} ({{ $model->percentage }}%)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="idp_competency_type" class="form-label">Competency Type <span class="text-danger">*</span></label>
                <select id="idp_competency_type" name="competency_type" class="form-select" required>
                    <option value="">Please select</option>
                    <option value="Soft Competency">Soft Competency</option>
                    <option value="Technical Competency">Technical Competency</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="idp_competency_name" class="form-label">Competency Name <span class="text-danger">*</span></label>
                <select id="idp_competency_name" name="competency_name" placeholder="Select or type..." required>
                    <option value="">Select or type...</option>
                    @foreach($competencyNameOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label for="idp_review_tools" class="form-label">Review Tools <span class="text-danger">*</span></label>
                <select id="idp_review_tools" name="review_tools" placeholder="Select or type..." required>
                    <option value="">Select or type...</option>
                     @foreach($reviewToolOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="p-3 rounded mb-3" style="background-color: #f8f9fa;">
        <h5 class="fw-bold text-primary">Development Program</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="idp_development_program" class="form-label">Development Program <span class="text-danger">*</span></label>
                <select id="idp_development_program" name="development_program" placeholder="Select or type..." required>
                     <option value="">Select or type...</option>
                     @foreach($developmentProgramOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="idp_expected_outcome" class="form-label">Expected Outcome <span class="text-danger">*</span></label>
                <input type="text" id="idp_expected_outcome" name="expected_outcome" class="form-control" placeholder="e.g., Fulfilling competency gaps" required>
            </div>
        </div>
    </div>

    <div class="p-3 rounded" style="background-color: #f8f9fa;">
        <h5 class="fw-bold text-primary">Timeline & Result</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label for="idp_time_frame_start" class="form-label">Time Frame <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="date" id="idp_time_frame_start" name="time_frame_start" class="form-control" required max="{{ date('Y-m-d') }}">
                    <span class="input-group-text">→</span>
                    <input type="date" id="idp_time_frame_end" name="time_frame_end" class="form-control" required max="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-6">
                <label for="idp_realization_date" class="form-label">Realization Date</label>
                <input type="date" id="idp_realization_date" name="realization_date" class="form-control" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label for="idp_result_evidence" class="form-label">Result Evidence</label>
                <input type="text" id="idp_result_evidence" name="result_evidence" class="form-control" placeholder="e.g., Certificate Link">
            </div>
        </div>
    </div>
</div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const competencyMap = @json($competencyMap);
        const allDevelopmentPrograms = @json($developmentProgramOptions ?? []);
        const tomSelectSettings = {
            create: true, 
            sortField: {
                field: "text",
                direction: "asc"
            }
        };

        function toTitleCase(str) {
            return str.replace(
                /\w\S*/g,
                function(txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                }
            );
        }

        function initializeTomSelect(selector) {
            return new TomSelect(selector, {
                create: true,
                sortField: { field: "text", direction: "asc" },
                onOptionAdd: function(value, data) {
                    const existingOptions = this.options;
                    const formattedValue = toTitleCase(value);

                    // (case-insensitive)
                    for (const key in existingOptions) {
                        if (existingOptions[key].text.toLowerCase() === formattedValue.toLowerCase()) {
                            this.setValue(existingOptions[key].value);
                            this.removeOption(value); 
                            return; 
                        }
                    }
                    this.removeOption(value);
                    this.addOption({ 
                        value: formattedValue,
                        text: formattedValue
                    });
                    this.setValue(formattedValue);
                }
            });
        }
        const competencyNameSelect = new TomSelect("#idp_competency_name", tomSelectSettings);
        const devProgramSelect = new TomSelect("#idp_development_program", tomSelectSettings);
        const reviewToolsSelect = new TomSelect("#idp_review_tools", tomSelectSettings);

        competencyNameSelect.on('change', function(value) {
            const relatedPrograms = competencyMap[value];
            const currentValue = devProgramSelect.getValue();
            devProgramSelect.clear();
            devProgramSelect.clearOptions();

             if (relatedPrograms && relatedPrograms.length > 0) {
                // Jika ADA program yang berelasi, tambahkan HANYA ITU ke dropdown
                relatedPrograms.forEach(program => {
                    devProgramSelect.addOption({ value: program, text: program });
                });
            } else {
                // Jika TIDAK ADA, atau jika pilihan pertama dikosongkan,
                // tampilkan kembali SEMUA pilihan program yang ada
                allDevelopmentPrograms.forEach(program => {
                    devProgramSelect.addOption({ value: program, text: program });
                });
            }

            // Coba atur kembali nilai sebelumnya jika masih ada di daftar pilihan yang baru
            if (devProgramSelect.options[currentValue]) {
                devProgramSelect.setValue(currentValue);
            }
        });

        const idpModal = document.getElementById('idpModal');
        const idpForm = document.getElementById('idpForm');

        idpModal.addEventListener('show.bs.modal', function () {
            competencyNameSelect.setValue(idpForm.querySelector('[name="competency_name"]').value);
            devProgramSelect.setValue(idpForm.querySelector('[name="development_program"]').value);
            reviewToolsSelect.setValue(idpForm.querySelector('[name="review_tools"]').value);
        });
        
        idpModal.addEventListener('hidden.bs.modal', function () {
            competencyNameSelect.clear();
            devProgramSelect.clear();
            reviewToolsSelect.clear();
        });
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