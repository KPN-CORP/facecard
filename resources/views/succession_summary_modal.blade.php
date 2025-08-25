<!-- Succession Summary Modal -->
<div class="modal fade" id="successionSummaryModal" tabindex="-1" aria-labelledby="successionSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('resultSummary.store') }}" method="POST" class="form-inline-update">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                <input type="hidden" name="form_type" value="succession_summary">

                <div class="modal-header">
                    <h5 class="modal-title" id="successionSummaryModalLabel">Edit Succession Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @can('input_critical_position')
                        <div class="col-md-12 mb-3">
                            <label for="modal_critical_position" class="form-label">Critical Position</label>
                            <select name="critical_position" id="modal_critical_position" class="form-select">
                                <option value="">Please select...</option>
                                <option value="No" @if(optional($resultSummary)->critical_position == 'No') selected @endif>No</option>
                                <option value="Yes" @if(optional($resultSummary)->critical_position == 'Yes') selected @endif>Yes</option>
                            </select>
                        </div>
                        @endcan
                        @can('input_successor_type')
                         <div class="col-md-12 mb-3">
                            <label for="modal_successor_type" class="form-label">Successor Type</label>
                            <select name="successor_type" id="modal_successor_type" class="form-select">
                                <option value="">Please select...</option>
                                <option value="SO (Ready Now)" @if(optional($resultSummary)->successor_type == 'SO (Ready Now)') selected @endif>SO (Ready Now)</option>
                                <option value="S1 (Ready 0-2 Years)" @if(optional($resultSummary)->successor_type == 'S1 (Ready 0-2 Years)') selected @endif>S1 (Ready 0-2 Years)</option>
                                <option value="S2 (Ready 2-5 Years)" @if(optional($resultSummary)->successor_type == 'S2 (Ready 2-5 Years)') selected @endif>S2 (Ready 2-5 Years)</option>
                                <option value="PS (Ready > 5 Years)" @if(optional($resultSummary)->successor_type == 'PS (Ready > 5 Years)') selected @endif>PS (Ready > 5 Years)</option>
                                <option value="ES (Emergency Successor)" @if(optional($resultSummary)->successor_type == 'ES (Emergency Successor)') selected @endif>ES (Emergency Successor)</option>
                            </select>
                        </div>
                        @endcan
                        @can('input_successor_position')
                        <div class="col-md-12">
                            <label for="modal_successor_to_position" class="form-label">Successor to Position</label>
                            <input type="text" name="successor_to_position" id="modal_successor_to_position" class="form-control" value="{{ optional($resultSummary)->successor_to_position }}">
                        </div>
                        @endcan
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
