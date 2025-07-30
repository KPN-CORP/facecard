<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<div class="modal fade" id="compAssessmentModal" tabindex="-1" aria-labelledby="compAssessmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"> 
    <div class="modal-content">
      <form id="compAssessmentForm" action="{{ route('competency.store') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
        <input type="hidden" id="matrixGradeHidden" name="matrix_grade">
        
        <div class="modal-header">
          <h1 class="modal-title fs-5 text-danger" id="compAssessmentModalLabel">Input Competency Assessment</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div class="row mb-3">
              <div class="col-md-6">
                  <label for="modalAssessmentDate" class="form-label">Assessment Date</label>
                  <input type="date" class="form-control" id="modalAssessmentDate" name="assessment_date" required>
              </div>
              <div class="col-md-6">
                  <label for="modalMatrixGradeDisplay" class="form-label">Matrix Grade (Target)</label>
                  <input type="text" class="form-control" id="modalMatrixGradeDisplay" readonly disabled>
              </div>
          </div>

          <div class="row mb-4">
            @can('input_proposed_grade')
              <div class="col-md-6">
                  <label for="proposed_grade_modal" class="form-label">Proposed Grade</label>
                  <select class="form-select" id="proposed_grade_modal" name="proposed_grade">
                      <option value="" disabled>Select Proposed Grade...</option>
                      @isset($uniqueGradeLevels)
                        @foreach($uniqueGradeLevels as $grade)
                            <option value="{{ $grade }}" @if(optional($resultSummary)->proposed_grade == $grade) selected @endif>{{ $grade }}</option>
                        @endforeach
                      @endisset
                  </select>
              </div>
              @endcan
              @can('input_priority_dev')
              <div class="col-md-6">
                  <label for="priority_for_development_modal" class="form-label">Priority for Development</label>
                  <select class="form-select" id="priority_for_development_modal" name="priority_for_development">
                      <option value="No" @if(optional($resultSummary)->priority_for_development == 'No') selected @endif>No</option>
                      <option value="Yes" @if(optional($resultSummary)->priority_for_development == 'Yes') selected @endif>Yes</option>
                  </select>
              </div>
              @endcan
          </div>

          {{-- Section 1: Core Competencies --}}
          <div class="competency-section bg-light p-3 rounded mb-4">
            <h5 class="mb-3 text-danger">Core Competencies</h5>
            <div class="row g-3">
                <div class="col-md">
                    <label for="synergized_team_score" class="form-label">Synergized Team</label>
                    <select id="synergized_team_score" name="synergized_team_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md">
                    <label for="integrity_score" class="form-label">Integrity</label>
                    <select id="integrity_score" name="integrity_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md">
                    <label for="growth_score" class="form-label">Growth</label>
                    <select id="growth_score" name="growth_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md">
                    <label for="adaptive_score" class="form-label">Adaptive</label>
                    <select id="adaptive_score" name="adaptive_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md">
                    <label for="passion_score" class="form-label">Passion</label>
                    <select id="passion_score" name="passion_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
            </div>
          </div>

          {{-- Section 2: Leader Competencies --}}
          <div class="competency-section bg-light p-3 rounded">
            <h5 class="mb-3 text-danger">Leader Competencies</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="manage_planning_score" class="form-label">Manage & Planning</label>
                    <select id="manage_planning_score" name="manage_planning_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="decision_making_score" class="form-label">Decision Making</label>
                    <select id="decision_making_score" name="decision_making_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="relationship_building_score" class="form-label">Relationship Building</label>
                    <select id="relationship_building_score" name="relationship_building_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="developing_others_score" class="form-label">Developing Others</label>
                    <select id="developing_others_score" name="developing_others_score" class="form-select">
                        <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                    </select>
                </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Save Assessment</button>
        </div>
      </form>
    </div>
  </div>
</div>
