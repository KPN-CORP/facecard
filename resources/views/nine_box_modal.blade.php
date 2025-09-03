
<div class="modal fade" id="nineBoxMappingModal" tabindex="-1" aria-labelledby="nineBoxMappingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="nineBoxMappingForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="nineBoxMappingModalLabel">Edit 9-Box Data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Year</label>
                        <input type="text" id="edit_appraisal_year" class="form-control bg-light text-dark" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Performance Appraisal</label>
                        <input type="text" id="edit_performance_grade" class="form-control bg-light text-dark" readonly>
                    </div>
                     </div>
                    <hr>
                    <div class="mb-3">
                        <label for="edit_potential" class="form-label">Potential *</label>
                        <select id="edit_potential" name="potential" class="form-select">
                            <option value="">Please select</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_talent_box" class="form-label">Talent Box *</label>
                        <select id="edit_talent_box" name="talent_box" class="form-select">
                            <option value="">Please select</option>
                            <option value="Stars (1)">Stars (1)</option>
                            <option value="High Potentials (2)">High Potentials (2)</option>
                            <option value="High Impact Performers (3)">High Impact Performers (3)</option>
                            <option value="Trusted Professional (4)">Trusted Professional (4)</option>
                            <option value="Potential Gems (5)">Potential Gems (5)</option>
                            <option value="Core Players (6)">Core Players (6)</option>
                            <option value="Effective Employee (7)">Effective Employee (7)</option>
                            <option value="Inconsistent Performers (8)">Inconsistent Performers (8)</option>
                            <option value="Deadwood (9)">Deadwood (9)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>