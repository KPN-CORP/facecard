@extends('layouts.app')
@include('succession_summary_modal')

@section('title', 'Employee Profile - ' . $employee->fullname)


@section('content')
<div class="container-fluid">
     <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('facecard.list') }}" class="btn-back">
            &laquo; Back to Face Card List
        </a>
    </div>

    @php
    $isIdpTabActive = collect(request()->keys())->some(function ($key) {
        return str_starts_with($key, 'page_model_') || $key === 'page_uncategorized';
    });
@endphp

{{-- Navigation Tab --}}
<ul class="nav nav-pills mb-4" id="mainTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link @if(!$isIdpTabActive) active @endif" id="face-card-tab" data-bs-toggle="pill" data-bs-target="#faceCardTab" type="button" role="tab" aria-controls="faceCardTab" aria-selected="{{ !$isIdpTabActive ? 'true' : 'false' }}">Face Card</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link @if($isIdpTabActive) active @endif" id="idp-tab" data-bs-toggle="pill" data-bs-target="#individualDevelopmentPlanTab" type="button" role="tab" aria-controls="individualDevelopmentPlanTab" aria-selected="{{ $isIdpTabActive ? 'true' : 'false' }}">Individual Development Plan</button>
    </li>
</ul>

    <div class="tab-content" id="mainTabContent">

        {{-- ======== TAB "FACE CARD" ========= --}}
         <div class="tab-pane fade @if(!$isIdpTabActive) show active @endif" id="faceCardTab" role="tabpanel" aria-labelledby="face-card-tab">
            
            @include('input_comp_assessment_modal')
            @include('nine_box_modal')

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><strong>Current Date & Time:</strong> {{ now()->format('d F Y H:i A') }}</div>
            </div>

            {{-- Section 1: Summaries & Photo --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="section-title">Individual Summary</h2>
                            <hr class="mt-0">
                            <table class="table-summary"><tbody>
                                <tr><td class="label-col">Full Name</td><td class="colon-col">:</td><td class="value-col">{{ $employee->fullname }}</td></tr>
                                <tr><td class="label-col">Date of Birth</td><td class="colon-col">:</td><td class="value-col">{{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}</td></tr>
                                <tr><td class="label-col">Marital Status</td><td class="colon-col">:</td><td class="value-col">{{ $employee->marital_status }}</td></tr>
                                <tr><td class="label-col">Language Ability</td><td class="colon-col">:</td><td class="value-col"> 
                                    @if(!empty($employee->language_ability) && is_array($employee->language_ability))
                                    {{ implode(', ', $employee->language_ability) }}
                                    @else
                                    N.A.
                                    @endif
                                </td>
                            </tr>
                                <tr><td class="label-col">Gender</td><td class="colon-col">:</td><td class="value-col">{{ $employee->gender }}</td></tr>
                                <tr><td class="label-col">Age</td><td class="colon-col">:</td><td class="value-col">{{ \Carbon\Carbon::parse($employee->date_of_birth)->age }} Year</td></tr>
                                <tr><td class="label-col">Family Location</td><td class="colon-col">:</td><td class="value-col">{{ $employee->permanent_city ?: 'N.A.' }}</td></tr>
                                <tr><td class="label-col">Nationality</td><td class="colon-col">:</td><td class="value-col">{{ $employee->nationality }}</td></tr>
                                <tr><td class="label-col">Homebase</td><td class="colon-col">:</td><td class="value-col">{{ $employee->homebase }}</td></tr>
                            </tbody></table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">

            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="section-title text-center mb-0">Employment Summary</h2>
                            @can('input_employment_summary')
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#successionSummaryModal" title="Edit Succession Summary">+ Input</button>
                            @endcan
                        </div>
                        <hr class="mt-2">
                        <div class="row">
                        <div class="col-md-6">
                        <table class="table-summary">
                            <tbody>
                            <tr><td class="label-col">Employee ID</td><td class="colon-col">:</td><td class="value-col">{{ $employee->employee_id }}</td></tr>
                            <tr><td class="label-col">Business Unit</td><td class="colon-col">:</td><td class="value-col">{{ $employee->group_company }}</td></tr>
                            <tr><td class="label-col">Company</td><td class="colon-col">:</td><td class="value-col">{{ $employee->company_name }}</td></tr>
                            <tr><td class="label-col">Position</td><td class="colon-col">:</td><td class="value-col">{{ $employee->designation_name ?: $employee->designation }}</td></tr>
                            <tr><td class="label-col">Department</td><td class="colon-col">:</td><td class="value-col">{{ $employee->unit }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table-summary">
                        <tbody>
                            <tr><td class="label-col">Division</td><td class="colon-col">:</td><td class="value-col">{{ $employee->group_company }}</td></tr>
                            <tr><td class="label-col">Work Location</td><td class="colon-col">:</td><td class="value-col">{{ $employee->office_area }}</td></tr>
                            <tr><td class="label-col">BU Join Date</td><td class="colon-col">:</td><td class="value-col">{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') }}</td></tr>
                            <tr><td class="label-col">KPN Join Date</td><td class="colon-col">:</td><td class="value-col">{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') }}</td></tr>
                            <tr><td class="label-col">Latest Performance</td><td class="colon-col">:</td><td class="value-col">
                                @if($latestAppraisal = $performanceAppraisals->first())
                                    {{ $latestAppraisal->appraisal_year }} (<strong>{{ $latestAppraisal->grade }}</strong>)
                                @else
                                    No data
                                @endif
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <hr class="mt-2 mb-2">
            <div class="row">
                <div class="col-md-6">
                    <table class="table-summary">
                        <tbody>
                            @can('view_critical_position')
                            <tr><td class="label-col">Critical Position</td><td class="colon-col">:</td><td class="value-col">{{ optional($resultSummary)->critical_position ?? 'N/A' }}</td></tr>
                            @endcan
                            @can('view_successor_type')
                            <tr><td class="label-col">Successor Type</td><td class="colon-col">:</td><td class="value-col">{{ optional($resultSummary)->successor_type ?? 'N/A' }}</td></tr>
                            @endcan
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table-summary">
                        <tbody>
                            @can('view_successor_position')
                            <tr><td class="label-col">Successor to Position</td><td class="colon-col">:</td><td class="value-col">{{ optional($resultSummary)->successor_to_position ?? 'N/A' }}</td></tr>
                            @endcan
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
                <div class="col-lg-2">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body p-2 d-flex align-items-center justify-content-center">
                             @if($employee->photo)
                                <img src="{{ asset($employee->photo) }}" class="img-fluid rounded" alt="Foto {{ $employee->fullname }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted w-100 h-100 rounded">
                                    <span>Employee Photo</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

{{-- Section 2: Education & Work Experience --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="section-title">Formal Education</h2>
                <hr class="mt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                        <thead class="table-light align-middle">
                            <tr>
                                <th>Period</th>
                                <th>Formal Education</th>
                                <th>Institution</th>
                                <th>Major</th>
                                <th>GPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($formalEducations as $edu)
                            <tr>
                                <td>{{$edu->from_date}} - {{$edu->to_date}}</td>
                                <td>{{$edu->degree}}</td>
                                <td>{{$edu->institution}}</td>
                                <td>{{$edu->major}}</td>
                                <td>{{ number_format($edu->gpa_percentage, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">No formal education data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="section-title">Work Experience</h2>
                <hr class="mt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                        <thead class="table-light align-middle">
                            <tr>
                                <th>Company</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Join Date</th>
                                <th>Resign Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workExperiences as $work)
                            <tr>
                                <td>{{$work->previous_company_name}}</td>
                                <td>{{$work->title}}</td>
                                <td>{{$work->summary}}</td>
                                <td>{{\Carbon\Carbon::parse($work->from_date)->format('d/m/Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($work->to_date)->format('d/m/Y')}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">No work experience data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section 3 & 4: Training/Certification & Internal Movement --}}
<div class="row g-4 mb-4">
    {{-- Training/Certification Column --}}
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="section-title">Training/Certification</h2>
                <hr class="mt-0">
                <div class="table-responsive">
                    {{-- Style matched to the other tables --}}
                    <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                        <thead class="table-light align-middle">
                            <tr>
                                <th>Training Name</th>
                                <th>Organizer</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trainings as $train)
                            <tr>
                                <td>{{$train->training_name}}</td>
                                <td>{{$train->organizer}}</td>
                                <td>{{\Carbon\Carbon::parse($train->start_date)->format('d M Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($train->end_date)->format('d M Y')}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">No training data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Internal Movement Column --}}
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="section-title">Internal Movement</h2>
                <hr class="mt-0">
                <div class="table-responsive">
                    {{-- Style matched to the other tables --}}
                    <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                        <thead class="table-light align-middle">
                            <tr>
                                <th>Employment Period</th>
                                <th>Grade</th>
                                <th>Promotion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($internalMovements as $movement)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($movement->from_date)->format('d M Y') }} - {{ $movement->to_date ? \Carbon\Carbon::parse($movement->to_date)->format('d M Y') : 'Present' }}</td>
                                <td>{{ $movement->job_level }}</td>
                                <td>{{ $movement->is_promotion }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center p-4">No internal movement data found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

            {{-- Section 4: Year-on-Year 9-Box Mapping --}}
            @can('view_idp_report')
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center position-relative">
                            <h2 class="section-title">Year-on-Year 9-Box Mapping</h2>
                        </div>
                        <hr class="mt-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Year</th>
                                        <th>Performance Appraisal</th>
                                        <th>Potential</th>
                                        <th>Talent Box</th>
                                        @can('input_year_on_year')
                                        <th>Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($performanceAppraisals as $appraisal)
                                        <tr>
                                            <td>{{ $appraisal->appraisal_year }}</td>
                                            <td>{{ $appraisal->grade }}</td>
                                            <td>{{ $appraisal->talent_status ?? '-' }}</td>
                                            <td>{{ $appraisal->talent_box ?? '-' }}</td>
                                             @can('input_year_on_year')
                                            <td>
                                                <button class="btn btn-sm btn-outline-warning" onclick='app.openNineBoxEditModal({{ json_encode($appraisal) }})'>Edit</button>
                                            </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="p-4">No performance appraisal data found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- Section 5: Competency Assessment --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <h2 class="section-title mb-0">Competency Assessment</h2>
                    </div>
                <hr class="mt-2">

        {{-- Row 2: Combined Controls in a single row --}}
<div class="row g-3 align-items-end mb-4">
    <div class="col-md-2">
        <label class="form-label text-muted">Assessment Status</label>
        <div class="form-control d-flex align-items-center justify-content-center p-0 @if($needsRenewal) bg-danger-subtle text-danger @else bg-success-subtle text-success @endif" style="height: calc(1.5em + 0.75rem + 2px);">
            <span class="fw-medium">
                @if($needsRenewal) Needs Renewal @else Up to Date @endif
            </span>
        </div>
    </div>
    @can('view_proposed_grade')
    <div class="col-md-2">
        <label for="proposed_grade" class="form-label text-muted">Proposed Grade</label>
        <input type="text" id="proposed_grade" class="form-control bg-light text-dark" disabled value="{{ optional($resultSummary)->proposed_grade ?? 'N/A' }}" readonly>
    </div>
    @endcan
    @can('view_priority_dev')
    <div class="col-md-2">
        <label for="priority_for_development" class="form-label text-muted">Priority For Development</label>
        <input type="text" id="priority_for_development" class="form-control bg-light text-dark" disabled value="{{ optional($resultSummary)->priority_for_development ?? 'N/A' }}" readonly>
    </div>
    @endcan
    <div class="col">
        <label for="assessmentDate" class="form-label">
            Assessment Date
            {{-- to show latest date --}}
            <span id="latestDateIndicator" class="text-info-blue bg-info-subtle p-1 px-2 rounded small fw-normal ms-1"></span>
        </label>
        <input type="date" id="assessmentDate" class="form-control">
    </div>
    <div class="col">
        <label for="matrixGradeSelect" class="form-label">
            Matrix Grade
            {{-- to show latest grade Elemen --}}
            <span id="latestGradeIndicator" class="text-info-blue bg-info-subtle p-1 px-2 rounded small fw-normal ms-1"></span>
        </label>
        <select id="matrixGradeSelect" class="form-select">
            <option value="">Select Date</option>
        </select>
    </div>
    @can('input_competency_assessment')
    <div class="col-md-1">
        <button class="btn btn-outline-danger w-100" onclick="app.openInputModal()">+ Input</button>
    </div>
    @endcan
</div>
<hr>
        
        {{-- Chart and Tables --}}
        <div class="row g-4 mt-2">
            <div class="col-lg-4">
                <canvas id="competencyRadarChart" style="max-height: 400px;"></canvas>
            </div>
            <div class="col-lg-4">
                <table class="competency-evaluation-table">
                    <thead><tr><th style="width: 35%;" id="overallStatusCell">N.A.</th><th id="competencyFitCount">0 Fit</th><th id="overallExcelProficientStatusCell">N.A.</th></tr></thead>
                    <tbody>
                        <tr class="competency-level-row" data-fit-level="9"><td class="indicator-group-cell" rowspan="3">Sesuai</td><td>9 Kompetensi Fit</td><td>Excel</td></tr>
                        <tr class="competency-level-row" data-fit-level="8"><td>8 Kompetensi Fit</td><td class="excel-status-cell" rowspan="2">Proficient</td></tr>
                        <tr class="competency-level-row" data-fit-level="7"><td>7 Kompetensi Fit</td></tr>
                        <tr class="competency-level-row" data-fit-level="6"><td class="indicator-group-cell" rowspan="3">Butuh Pengembangan</td><td>6 Kompetensi Fit</td><td class="excel-status-cell" rowspan="2">Competent</td></tr>
                        <tr class="competency-level-row" data-fit-level="5"><td>5 Kompetensi Fit</td></tr>
                        <tr class="competency-level-row" data-fit-level="4"><td>4 Kompetensi Fit</td><td class="excel-status-cell" rowspan="2">Need Development</td></tr>
                        <tr class="competency-level-row" data-fit-level="3"><td class="indicator-group-cell" rowspan="3">Tidak Sesuai</td><td>3 Kompetensi Fit</td></tr>
                        <tr class="competency-level-row" data-fit-level="2"><td>2 Kompetensi Fit</td><td class="excel-status-cell" rowspan="2">Significantly Need Development</td></tr>
                        <tr class="competency-level-row" data-fit-level="1"><td>1 Kompetensi Fit</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                            <div class="row g-0">
                                <div class="col-6">
                                    <table class="table table-bordered table-sm strength-dev-table mb-0">
                                        <thead><tr><th>Strength</th></tr></thead>
                                        <tbody id="strengthList">
                                            @for ($i = 0; $i < 8; $i++)
                                                <tr><td>&nbsp;</td></tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-bordered table-sm strength-dev-table mb-0">
                                        <thead><tr><th>Area of Development</th></tr></thead>
                                        <tbody id="areaOfDevelopmentList">
                                            @for ($i = 0; $i < 8; $i++)
                                                <tr><td>&nbsp;</td></tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======== TAB "INDIVIDUAL DEVELOPMENT PLAN" ========= --}}
        <div class="tab-pane fade @if($isIdpTabActive) show active @endif" id="individualDevelopmentPlanTab" role="tabpanel" aria-labelledby="idp-tab">
<div class="d-flex justify-content-between align-items-start mb-4 p-3 border rounded bg-light">
    <div>
        <h5 class="mb-1 fw-bold">{{ $employee->fullname }}</h5>
        <span class="text-muted">{{ $employee->employee_id }}</span>
    </div>
    <div class="text-end">
    <small class="text-muted">Current Date & Time</small>
    <div class="fw-bold">{{ now()->format('d F Y, H:i A') }}</div>
</div>
</div>
           @include('idp_table')
        </div>
    </div> 
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
 if (typeof window.app === 'undefined') { window.app = {}; }

 app.openTab = function(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    document.querySelectorAll('.tab-links').forEach(tl => tl.classList.remove('active'));
    const currentTab = document.getElementById(tabName);
    if(currentTab) {
        currentTab.classList.add('active');
    }
    if(evt.currentTarget) {
        evt.currentTarget.classList.add('active');
    }
};

document.addEventListener("DOMContentLoaded", function() {
    const allMatrixGrades = @json($allMatrixGrades ?? []);
    const latestAssessment = @json($latestAssessment);
    const competencies = @json($competencyNames ?? []);
    const allAssessmentsByYear = @json($assessmentsForJs);
    const competencyKeys = ['synergized_team', 'integrity', 'growth', 'adaptive', 'passion', 'manage_planning', 'decision_making', 'relationship_building', 'developing_others'];

    const dateInput = document.getElementById('assessmentDate');
    const gradeSelect = document.getElementById('matrixGradeSelect');
    let radarChartInstance = null;

    function initializePage() {
        if (!dateInput || !gradeSelect) return; 
        
        dateInput.value = latestAssessment?.assessment_date 
            ? new Date(latestAssessment.assessment_date).toISOString().split('T')[0] 
            : ''; 

        dateInput.addEventListener('change', handleDateChange);
        gradeSelect.addEventListener('change', updateDisplayFromJS);
        
        handleDateChange();
    }

    function handleDateChange() {
        const year = dateInput.value ? String(new Date(dateInput.value).getFullYear()) : null;
        if (!year) {
            gradeSelect.innerHTML = '<option value="">Select Date First</option>';
            resetUI();
            return;
        }

        const gradesForYear = allMatrixGrades[year] || [];
        gradeSelect.innerHTML = gradesForYear.length 
            ? '<option value="">Select Grade</option>' 
            : '<option value="">No Grades for this Year</option>';
        
        gradesForYear.forEach(config => gradeSelect.add(new Option(config.grade_level, config.grade_level)));
       
        const assessmentForYear = allAssessmentsByYear[year];
        if (assessmentForYear) {
            gradeSelect.value = assessmentForYear.matrix_grade;
        }
        
        updateDisplayFromJS();
    }

    function updateDisplayFromJS() {
    const year = dateInput.value ? String(new Date(dateInput.value).getFullYear()) : null;
    const grade = gradeSelect.value;
    
    if (!year || !grade) {
        resetUI();
        return;
    }

    const targetConfig = allMatrixGrades[year]?.find(g => g.grade_level === grade);
    if (!targetConfig) {
        resetUI();
        return;
    }

    const assessmentForYear = allAssessmentsByYear[year];
    const actualData = assessmentForYear ? competencyKeys.map(key => assessmentForYear[key + '_score'] ?? 0) : [];
    const targetData = competencyKeys.map(key => targetConfig[key + '_min'] ?? 0);
    const result = calculateCompetencyResult(targetData, actualData);
    
    // Logic to show latest data assessment date and grade per period
    const dateIndicator = document.getElementById('latestDateIndicator');
    const gradeIndicator = document.getElementById('latestGradeIndicator');

    if (assessmentForYear) {
        const dateObj = new Date(assessmentForYear.assessment_date);
        const formattedDate = `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
        
        dateIndicator.textContent = `(Latest: ${formattedDate})`;
        gradeIndicator.textContent = `(Latest: ${assessmentForYear.matrix_grade})`;
    } else {
        dateIndicator.textContent = '';
        gradeIndicator.textContent = '';
    }

    redrawRadarChart(actualData, targetData);
    updateUIElements(result);
}
    function calculateCompetencyResult(targetScores, actualScores) {
        let strengths = [], areas = [], fitCount = 0;
        
        if (actualScores.length > 0 && actualScores.some(score => score > 0)) {
            competencies.forEach((name, i) => {
                if ((actualScores[i] ?? 0) >= targetScores[i]) strengths.push(name);
                else areas.push(name);
            });
            fitCount = strengths.length;
        }

        let overallStatus = 'N.A.', excelStatus = 'N.A.';
        if (fitCount > 0) {
            if (fitCount >= 7) overallStatus = 'Sesuai';
            else if (fitCount >= 4) overallStatus = 'Butuh Pengembangan';
            else overallStatus = 'Tidak Sesuai';

            if (fitCount >= 9) excelStatus = 'Excel';
            else if (fitCount >= 7) excelStatus = 'Proficient';
            else if (fitCount >= 5) excelStatus = 'Competent';
            else if (fitCount >= 3) excelStatus = 'Need Development';
            else excelStatus = 'Significantly Need Development';
        }
        return { strengths, areasOfDevelopment: areas, fitCount, overallStatus, excelStatus };
    }
    
     function updateUIElements(result) {
        document.getElementById('overallStatusCell').textContent = result.overallStatus;
        document.getElementById('competencyFitCount').textContent = `${result.fitCount} Kompetensi Fit`;
        document.getElementById('overallExcelProficientStatusCell').textContent = result.excelStatus;

        let strengthHtml = '';
        result.strengths.forEach(s => {
            strengthHtml += `<tr><td>${s}</td></tr>`;
        });
        // Fill remaining rows to make 8 total
        for (let i = result.strengths.length; i < 8; i++) {
            strengthHtml += `<tr><td>&nbsp;</td></tr>`;
        }
        document.getElementById('strengthList').innerHTML = strengthHtml;

        // Corrected logic to generate table rows for Areas of Development
        let areaHtml = '';
        result.areasOfDevelopment.forEach(a => {
            areaHtml += `<tr><td>${a}</td></tr>`;
        });
        // Fill remaining rows to make 8 total
        for (let i = result.areasOfDevelopment.length; i < 8; i++) {
            areaHtml += `<tr><td>&nbsp;</td></tr>`;
        }
        document.getElementById('areaOfDevelopmentList').innerHTML = areaHtml;

        highlightTableRow(result.fitCount);
    }
    
    function resetUI() {
        const year = dateInput.value ? new Date(dateInput.value).getFullYear() : null;
        const grade = gradeSelect.value;
        const targetConfig = allMatrixGrades[year]?.find(g => g.grade_level === grade);
        const targetData = targetConfig ? competencyKeys.map(key => targetConfig[key + '_min'] ?? 0) : [];

        redrawRadarChart([], targetData);
        updateUIElements({strengths: [], areasOfDevelopment: [], fitCount: 0, overallStatus: 'N.A.', excelStatus: 'N.A.'});
        document.getElementById('latestDateIndicator').textContent = '';
    document.getElementById('latestGradeIndicator').textContent = '';
    }

    function redrawRadarChart(actualData, targetData) {
        const ctx = document.getElementById('competencyRadarChart')?.getContext('2d');
        if (!ctx) return;
        if (radarChartInstance) radarChartInstance.destroy();
        
        const datasets = [];
        if (targetData.length > 0) datasets.push({ label: 'Target', data: targetData, borderColor: 'rgb(255, 99, 132)', backgroundColor: 'rgba(255, 99, 132, 0.2)'});
        if (actualData.length > 0) datasets.push({ label: 'Actual', data: actualData, borderColor: 'rgb(54, 162, 235)', backgroundColor: 'rgba(54, 162, 235, 0.2)'});

        if (datasets.length === 0) {
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            ctx.font = "16px Arial"; ctx.textAlign = "center"; ctx.fillStyle = "#888";
            ctx.fillText("Select a Grade to see comparison.", ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }
        radarChartInstance = new Chart(ctx, { type: 'radar', data: { labels: competencies, datasets: datasets }, options: { responsive: true, maintainAspectRatio: false, scales: { r: { suggestedMin: 0, suggestedMax: 4, ticks: { stepSize: 1 } } } }});
    }

    function highlightTableRow(fitCount) {
        document.querySelectorAll('.competency-level-row').forEach(row => {
            row.classList.remove('active-highlight');
            if (fitCount > 0 && parseInt(row.dataset.fitLevel) === fitCount) row.classList.add('active-highlight');
        });
    }

   app.openModal = function(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        } else {
            console.error(`Modal with ID "${modalId}" not found.`);
        }
    };

    app.openNineBoxEditModal = function(appraisal) {
        const form = document.getElementById('nineBoxMappingForm');
        if (!form) {
            console.error('Form dengan ID "nineBoxMappingForm" tidak ditemukan.');
            return;
        }
        
        form.action = `/performance-appraisal/${appraisal.id}`;

        document.getElementById('edit_appraisal_year').value = appraisal.appraisal_year;
        document.getElementById('edit_performance_grade').value = appraisal.grade;
        document.getElementById('edit_talent_status').value = appraisal.talent_status || "";
        document.getElementById('edit_talent_box').value = appraisal.talent_box || "";
        
        app.openModal('nineBoxMappingModal');
    };



    app.closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if(modal) modal.style.display = 'none';
    }

    app.openInputModal = function() {
        const dateValue = dateInput.value;
        const gradeValue = gradeSelect.value;
        if (!dateValue || !gradeValue) {
            alert('Please select an Assessment Date and Matrix Grade first.');
            return;
        }
        const modal = document.getElementById('compAssessmentModal');
        if (!modal) return;
        
        const modalElement = document.getElementById('compAssessmentModal');
        if (!modalElement) {
            console.error('Modal element not found!');
            return;
        }
        
        modalElement.querySelector('#modalMatrixGradeDisplay').value = gradeValue;
        modalElement.querySelector('#matrixGradeHidden').value = gradeValue;
        modalElement.querySelector('#modalAssessmentDate').value = dateValue;
        modalElement.querySelectorAll('select[name*="_score"]').forEach(s => s.value = '0');
        
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    }
    
    app.openCreateIdpModal = function() {
        const form = document.getElementById('idpForm');
        if(!form) return;

        form.reset();
        form.action = "{{ route('idp.store') }}";
        document.getElementById('idpModalTitle').textContent = "Input Development Plan";
        document.getElementById('idpFormMethod').innerHTML = ""; // No method spoofing for create
        
        form.querySelectorAll('input, select, textarea').forEach(el => { 
            el.readOnly = false; 
            if(el.tagName === 'SELECT') el.disabled = false;
        });

        app.openModal('idpModal');
    };

   app.openEditIdpModal = function(plan) {
        Swal.fire({
            title: 'Edit Data?',
            text: "Anda akan mengubah data Development Plan ini.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, edit!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('idpForm');
                if(!form) return;

                form.reset();
                form.action = `/idp/update/${plan.id}`; 
                document.getElementById('idpModalTitle').textContent = "Edit Development Plan";
                document.getElementById('idpFormMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';

                for (const key in plan) {
                    const el = form.querySelector(`[name="${key}"]`);
                    if (el) {
                        if (['time_frame_start', 'time_frame_end', 'realization_date'].includes(key) && plan[key]) {
                            el.value = new Date(plan[key]).toISOString().split('T')[0];
                        } else {
                            el.value = plan[key];
                        }
                    }
                }

    form.querySelectorAll('input, select, textarea').forEach(el => {
        el.readOnly = false;
        if(el.tagName === 'SELECT') {
            el.disabled = false;
        }
    });
    
    app.openModal('idpModal');
}
  });
    };

    const deleteForms = document.querySelectorAll('.form-delete-idp');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        event.target.submit();
                    }
                });
            });
        });

        const updateForms = document.querySelectorAll('.form-inline-update');
        updateForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Save Changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#c82333',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Save!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        event.target.submit(); 
                    }
                });
            });
        });
    
    initializePage();
});
</script>
@endpush
