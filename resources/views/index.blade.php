@extends('layouts.app')
@include('succession_summary_modal')

@section('title', 'Employee Profile - ' . $employee->fullname)

@section('content')
<div class="container-fluid">
    
    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('facecard.list') }}" class="text-decoration-none text-primary fw-medium">
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

            <div class="mb-3">
    <span class="text-muted">Published Date :</span>
    <span class="fw-semibold text-dark">
        @if($lastUpdatedTimestamp)
            {{ \Carbon\Carbon::parse($lastUpdatedTimestamp)->format('d F Y, H:i A') }}
        @else
            N/A
        @endif
    </span>
</div>

            {{-- Section 1: Summaries & Photo --}}
            <div class="row g-4 mb-4">
                {{-- Individual Summary --}}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold ">Individual Summary</h5>
                            <hr>
                            <dl class="row mb-0" style="font-size: 0.9rem;">
                                <dt class="col-md-4 fw-semibold">Full Name</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->fullname }}</dd>

                                <dt class="col-md-4 fw-semibold">Date of Birth</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}</dd>

                                <dt class="col-md-4 fw-semibold">Age</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ \Carbon\Carbon::parse($employee->date_of_birth)->age }} Years Old</dd>

                                <dt class="col-md-4 fw-semibold">Marital Status</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ $employee->marital_status }}</dd>
                                
                                <dt class="col-md-4 fw-semibold">Gender</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ $employee->gender }}</dd>
                                
                                <dt class="col-md-4 fw-semibold">Nationality</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ $employee->nationality }}</dd>

                                <dt class="col-md-4 fw-semibold">Homebase</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ $employee->homebase }}</dd>

                                <dt class="col-md-4 fw-semibold">Family Location</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7"> {{ $employee->permanent_city ?: 'N.A.' }}</dd>

                                <dt class="col-md-4 fw-semibold">Language Ability</dt>
                                <dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">
                                     @if(!empty($employee->language_ability) && is_array($employee->language_ability))
                                        {{ implode(', ', $employee->language_ability) }}
                                     @else
                                        N.A.
                                     @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Employment Summary --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="row">
                                <div class="col-md-6">
                                <h5 class="card-title text-primary fw-bold mb-0">Employment Summary</h5>
                                </div>
                                <div class="col-md-6 text-end">
                                @can('input_employment_summary')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#successionSummaryModal" title="Edit Succession Summary">+ Input</button>
                                @endcan
                                </div>
                            </div>
                            <hr>
                            <div class="row" style="font-size: 0.9rem;">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-md-4 fw-semibold">Employee ID</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->employee_id }}</dd>
                                        <dt class="col-md-4 fw-semibold">Business Unit</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->group_company }}</dd>
                                        <dt class="col-md-4 fw-semibold">Company</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->company_name }}</dd>
                                        <dt class="col-md-4 fw-semibold">Position</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->designation_name ?: $employee->designation }}</dd>
                                        <dt class="col-md-4 fw-semibold">Department</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->unit }}</dd>
                                <dt class="col-md-4 fw-semibold">Work Location</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->office_area }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-md-4 fw-semibold">Division</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->group_company }}</dd>
                                        <dt class="col-md-4 fw-semibold">BU Join Date</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') }}</dd>
                                        <dt class="col-md-4 fw-semibold">KPN Join Date</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') }}</dd>
                                <dt class="col-md-4 fw-semibold">Current Grade</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ $employee->job_level}}</dd>
                                        <dt class="col-md-4 fw-semibold">Performance</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">
                                            @if($latestAppraisal = $performanceAppraisals->first())
                                                {{ $latestAppraisal->appraisal_year }} (<strong>{{ $latestAppraisal->grade }}</strong>)
                                            @else
                                                No data
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <hr class="my-2">
                             <div class="row" style="font-size: 0.9rem;">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        @can('view_critical_position')
                                        <dt class="col-md-4 fw-semibold">Critical Position</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ optional($resultSummary)->critical_position ?? 'N/A' }}</dd>
                                        @endcan
                                        @can('view_successor_type')
                                        <dt class="col-md-4 fw-semibold">Successor Type</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ optional($resultSummary)->successor_type ?? 'N/A' }}</dd>
                                        @endcan
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                         @can('view_successor_position')
                                        <dt class="col-md-4 fw-semibold">Successor to</dt><dd class="col-md-1 p-0 text-end">:</dd>
                                <dd class="col-md-7">{{ optional($resultSummary)->successor_to_position ?? 'N/A' }}</dd>
                                        @endcan
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

{{-- Photo --}}
<div class="col-md-2">
    <div class="card h-100 shadow-sm">
        <div class="card-body p-4 d-flex justify-content-center align-items-start">
                <div class="ratio ratio-1x1">
                    @if($employee->photo)
                        <img src="{{ asset($employee->photo) }}" class="rounded" alt="Foto {{ $employee->fullname }}" style="object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded p-3 text-center">
                            <small>Employee Photo</small>
                        </div>
                    @endif
                </div>
        </div>
    </div>
</div>

            {{-- Education & Work Experience --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary text-center fw-bold">Formal Education</h5><hr>
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
                                            <td class="text-start">{{$edu->institution}}</td>
                                            <td class="text-start">{{$edu->major}}</td>
                                            <td>{{ number_format($edu->gpa_percentage, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center p-4">No formal education data.</td></tr>
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
                            <h5 class="card-title text-primary text-center fw-bold">Work Experience</h5><hr>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                                    <thead class="table-light align-middle">
                                        <tr>
                                            <th>Join Date</th>
                                            <th>Resign Date</th>
                                            <th>Company</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($workExperiences as $work)
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse($work->from_date)->format('d/m/Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($work->to_date)->format('d/m/Y')}}</td>
                                            <td class="text-start">{{$work->previous_company_name}}</td>
                                            <td class="text-start">{{$work->title}}</td>
                                            <td>{{$work->summary}}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center p-4">No work experience data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Training & Internal Movement --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary text-center fw-bold">Training/Certification</h5><hr>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                                    <thead class="table-light align-middle">
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Training Name</th>
                                            <th>Organizer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trainings as $train)
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse($train->start_date)->format('d M Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($train->end_date)->format('d M Y')}}</td>
                                            <td class="text-start">{{$train->training_name}}</td>
                                            <td>{{$train->organizer}}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center p-4">No training data.</td></tr>
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
                            <h5 class="card-title text-primary text-center fw-bold">Internal Movement</h5><hr>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                                     <thead class="table-light align-middle">
                                        <tr>
                                            <th>Employment Period</th>
                                            <th>Business Unit</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Grade</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($internalMovements as $movement)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($movement->period_start)->format('d M Y') }} - {{ $movement->period_end ? \Carbon\Carbon::parse($movement->period_end)->format('d M Y') : 'Present' }}</td>
                                            <td>{{ $movement->business_unit ?? 'N/A' }}</td>
                                            <td>{{ $movement->department ?? 'N/A' }}</td>
                                            <td class="text-start">{{ $movement->position ?? 'N/A' }}</td>
                                            <td>{{ $movement->grade ?? 'N/A' }}</td>
                                            <td>{{ $movement->type }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center p-4">No internal movement data found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 9-Box Mapping --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary text-center fw-bold">Year-on-Year 9-Box Mapping</h5><hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" style="font-size: 0.9rem;">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Year</th>
                                        <th>Performance Appraisal</th>
                                        @can('input_year_on_year')
                                        <th>Potential</th>
                                        <th>Talent Box</th>
                                       <th>Action</th>
                                       @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($performanceAppraisals as $appraisal)
                                        <tr>
                                            <td>{{ $appraisal->appraisal_year }}</td>
                                            <td>{{ $appraisal->grade }}</td>
                                            @can('input_year_on_year')
                                            <td>{{ $appraisal->potential ?? '-' }}</td>
                                            <td>{{ $appraisal->talent_box ?? '-' }}</td>
                                            <td><button class="btn btn-sm btn-outline-warning" onclick='app.openNineBoxEditModal({{ json_encode($appraisal) }})'>Edit</button></td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr><td colspan="@can('input_year_on_year') 5 @else 4 @endcan" class="p-4">No performance appraisal data found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                <small class="text-muted fst-italic">
                    <strong>Note:</strong><br>
                    Rating given to Employees On-Target or Meet Expectation:<br>
                    &gt; 2023: Meet Expectation (ME)<br>
                    2023: C<br>
                    &le; 2023: B
                </small>
            </div>
                    </div>
                </div>

            {{-- Competency Assessment --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary text-center fw-bold">Competency Assessment</h5><hr>
                    
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col">
                            <label for="assessmentDate" class="form-label small">Assessment Date <span id="latestDateIndicator" class="badge bg-info-subtle text-info-emphasis fw-normal"></span></label>
                            <input type="date" id="assessmentDate" class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                        @can('view_proposed_grade')
                        <div class="col-md-2">
                            <label for="proposed_grade" class="form-label small text-muted">Proposed Grade</label>
                            <input type="text" id="proposed_grade" class="form-control bg-light" value="{{ optional($latestAssessment)->proposed_grade ?? 'N/A' }}" readonly>
                        </div>
                        @endcan
                        @can('view_priority_dev')
                        <div class="col-md-2">
                            <label for="priority_for_development" class="form-label small text-muted">Priority For Development</label>
                            <input type="text" id="priority_for_development" class="form-control bg-light" value="{{ optional($latestAssessment)->priority_for_development ?? 'N/A' }}" readonly>
                        </div>
                        @endcan
                        <div class="col">
                            <label for="matrixGradeSelect" class="form-label small">Matrix Grade <span id="latestGradeIndicator" class="d-none"></span></label>
                            <select id="matrixGradeSelect" class="form-select"><option value="">Select Date</option></select>
                        </div>
                        @can('input_competency_assessment')
                        <div class="col-md-auto">
                            <button class="btn btn-outline-primary w-100" onclick="app.openInputModal()">+ Input</button>
                        </div>
                        @endcan
                    </div><hr>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-lg-4 d-flex flex-column align-items-center">
    <canvas id="competencyRadarChart" style="max-height: 350px; max-width: 350px;"></canvas>
    <div class="row g-0 mt-3 text-center small justify-content-center flex-nowrap">
    <div class="col-auto bg-light border p-2" id="overallStatusCell">N.A.</div>
    <div class="col-auto bg-light border p-2" id="competencyFitCount">0 Fit</div>
</div>
</div>

    <div class="col-lg-4">
    <table class="table table-sm table-bordered text-center align-middle" style="font-size: 0.8rem;">
        <tbody>
            <tr class="competency-level-row" data-fit-level="9">
                <td rowspan="3" style="width: 40%;">Sesuai / Recommended</td>
                <td>9 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="8">
                <td>8 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="7">
                <td>7 Kompetensi Fit</td>
            </tr>

            <tr class="competency-level-row" data-fit-level="6">
                <td rowspan="3" style="width: 40%;">Butuh Pengembangan / Need Development</td>
                <td>6 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="5">
                <td>5 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="4">
                <td>4 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="3">
                <td rowspan="3" style="width: 40%;">Belum Sesuai / Not Recommended</td>
                <td>3 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="2">
                <td>2 Kompetensi Fit</td>
            </tr>
            <tr class="competency-level-row" data-fit-level="1">
                <td>1 Kompetensi Fit</td>
            </tr>
        </tbody>
    </table>
</div>
                        <div class="col-lg-4">
                            <div class="row g-0">
                                <div class="col-6">
                                    <table class="table table-sm table-bordered mb-0" style="font-size: 0.8rem;">
                                        <thead class="table-light"><tr><th class="text-center">Strength</th></tr></thead>
                                        <tbody id="strengthList">@for ($i = 0; $i < 8; $i++)<tr><td>&nbsp;</td></tr>@endfor</tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                     <table class="table table-sm table-bordered mb-0" style="font-size: 0.8rem;">
                                        <thead class="table-light"><tr><th class="text-center">Area of Development</th></tr></thead>
                                        <tbody id="areaOfDevelopmentList">@for ($i = 0; $i < 8; $i++)<tr><td>&nbsp;</td></tr>@endfor</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{-- ======== TAB "INDIVIDUAL DEVELOPMENT PLAN" ========= --}}
        <div class="tab-pane fade @if($isIdpTabActive) show active @endif" id="individualDevelopmentPlanTab" role="tabpanel" aria-labelledby="idp-tab">
            @include('idp_table', [
                'developmentModels' => $developmentModels,
                'paginatedPlans' => $paginatedPlans,
            ])
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
    const needsRenewal = {{ $needsRenewal ? 'true' : 'false' }};
    const competencies = @json($competencyNames ?? []);
    const allAssessmentsByYear = @json($assessmentsForJs);
    const competencyKeys = ['synergized_team', 'integrity', 'growth', 'adaptive', 'passion', 'manage_planning', 'decision_making', 'relationship_building', 'developing_others'];

    const dateInput = document.getElementById('assessmentDate');
    const gradeSelect = document.getElementById('matrixGradeSelect');
    let radarChartInstance = null;

    function initializePage() {
    if (!dateInput || !gradeSelect) return;

    if (latestAssessment) {
        dateInput.value = new Date(latestAssessment.assessment_date).toISOString().split('T')[0];
        handleDateChange(); 
    } else {
        const dateIndicator = document.getElementById('latestDateIndicator');
        if (dateIndicator) {
            dateIndicator.classList.remove('bg-info-subtle', 'text-info-emphasis');
            dateIndicator.classList.add('bg-danger-subtle', 'text-danger-emphasis');
            dateIndicator.textContent = '(No Assessment Data)';
        }
    }

    dateInput.addEventListener('change', handleDateChange);
    gradeSelect.addEventListener('change', updateDisplayFromJS);
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
    
    // Ambil semua elemen UI di awal agar mudah diakses
    const dateIndicator = document.getElementById('latestDateIndicator');
    const gradeIndicator = document.getElementById('latestGradeIndicator');
    const proposedGradeInput = document.getElementById('proposed_grade');
    const priorityDevInput = document.getElementById('priority_for_development');

    // Jika tahun belum dipilih, reset semuanya dan keluar
    if (!year) {
        resetUI();
        return;
    }

    const assessmentForYear = allAssessmentsByYear[year];

    if (proposedGradeInput && priorityDevInput) {
        if (assessmentForYear) {
            proposedGradeInput.value = assessmentForYear.proposed_grade || 'N/A';
            priorityDevInput.value = assessmentForYear.priority_for_development || 'N/A';
        } else {
            proposedGradeInput.value = 'N/A';
            priorityDevInput.value = 'N/A';
        }
    }

    // Perbarui Indikator Status Tanggal
    dateIndicator.classList.remove('bg-success-subtle', 'text-success-emphasis', 'bg-danger-subtle', 'text-danger-emphasis', 'bg-info-subtle', 'text-info-emphasis');
    if (assessmentForYear) {
        const dateObj = new Date(assessmentForYear.assessment_date);
        const formattedDate = `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
        
        if (needsRenewal) {
            dateIndicator.classList.add('bg-danger-subtle', 'text-danger-emphasis');
            dateIndicator.textContent = `(Expired from ${formattedDate})`;
        } else {
            dateIndicator.classList.add('bg-success-subtle', 'text-success-emphasis');
            dateIndicator.textContent = `(Up to Date from ${formattedDate})`;
        }
        gradeIndicator.textContent = `(Latest: ${assessmentForYear.matrix_grade})`;
    } else {
        dateIndicator.classList.add('bg-danger-subtle', 'text-danger-emphasis');
        dateIndicator.textContent = '(No assessment data for this year)';
        gradeIndicator.textContent = '';
    }

    if (!grade) {
        resetUI(false); 
        return;
    }
    const targetConfig = allMatrixGrades[year]?.find(g => g.grade_level === grade);
    if (!targetConfig) {
        resetUI();
        return;
    }
    
    // Perbarui Grade Indicator
    if(assessmentForYear) {
        gradeIndicator.textContent = `(Latest: ${assessmentForYear.matrix_grade})`;
    } else {
        gradeIndicator.textContent = '';
    }

    const actualData = assessmentForYear ? competencyKeys.map(key => assessmentForYear[key + '_score'] ?? 0) : [];
    const targetData = competencyKeys.map(key => targetConfig[key + '_min'] ?? 0);
    const result = calculateCompetencyResult(targetData, actualData);
    
    redrawRadarChart(actualData, targetData);
    updateUIElements(result);
}


    function calculateCompetencyResult(targetScores, actualScores) {
    let strengths = [],
        areas = [],
        fitCount = 0;

    if (actualScores.length > 0 && actualScores.some(score => score >= 1)) {
        competencies.forEach((name, i) => {
            if ((actualScores[i] ?? 0) >= targetScores[i] && (actualScores[i] ?? 0) >= 1) {
                strengths.push(name);
            } else {
                areas.push(name);
            }
        });
        fitCount = strengths.length;
    }

    let overallStatus = 'N.A.',
        excelStatus = 'N.A.';
    if (fitCount >= 1) {
        if (fitCount >= 7) overallStatus = 'Sesuai / Recommended';
        else if (fitCount >= 4) overallStatus = 'Butuh Pengembangan / Need Development';
        else overallStatus = 'Belum Sesuai / Not Recommended';

        if (fitCount >= 9) excelStatus = 'Excel';
        else if (fitCount >= 7) excelStatus = 'Proficient';
        else if (fitCount >= 5) excelStatus = 'Competent';
        else if (fitCount >= 3) excelStatus = 'Need Development';
        else excelStatus = 'Significantly Need Development';
    }
    return {
        strengths,
        areasOfDevelopment: areas,
        fitCount,
        overallStatus,
        excelStatus
    };
}
    
     function updateUIElements(result) {
        document.getElementById('overallStatusCell').textContent = result.overallStatus;
        document.getElementById('competencyFitCount').textContent = `${result.fitCount} Kompetensi Fit`;
        // document.getElementById('overallExcelProficientStatusCell').textContent = result.excelStatus;

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
    
    function resetUI(fullReset = true) {
    const year = dateInput.value ? new Date(dateInput.value).getFullYear() : null;
    const grade = gradeSelect.value;
    const targetConfig = allMatrixGrades[year]?.find(g => g.grade_level === grade);
    const targetData = targetConfig ? competencyKeys.map(key => targetConfig[key + '_min'] ?? 0) : [];

    redrawRadarChart([], targetData);
    updateUIElements({strengths: [], areasOfDevelopment: [], fitCount: 0, overallStatus: 'N.A.'});

    if (fullReset) {
        const dateIndicator = document.getElementById('latestDateIndicator');
        if (dateIndicator) {
            dateIndicator.textContent = '';
            dateIndicator.classList.remove('bg-success-subtle', 'text-success-emphasis', 'bg-danger-subtle', 'text-danger-emphasis');
        }
        document.getElementById('latestGradeIndicator').textContent = '';
        const proposedGradeInput = document.getElementById('proposed_grade');
        const priorityDevInput = document.getElementById('priority_for_development');
        if (proposedGradeInput && priorityDevInput) {
            proposedGradeInput.value = 'N/A';
            priorityDevInput.value = 'N/A';
        }
    }
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
    document.querySelectorAll('.competency-level-row td').forEach(cell => {
        cell.classList.remove('bg-success-subtle', 'text-black');
    });

    if (!fitCount || fitCount <= 0) {
        return;
    }

    const targetRow = document.querySelector(`.competency-level-row[data-fit-level="${fitCount}"]`);
    if (targetRow) {
        targetRow.querySelectorAll('td').forEach(cell => {
            cell.classList.add('bg-success-subtle', 'text-black');
        });
    }
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
        document.getElementById('edit_potential').value = appraisal.potential || "";
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

    const modalElement = document.getElementById('compAssessmentModal');
    if (!modalElement) {
        console.error('Modal element not found!');
        return;
    }

    modalElement.querySelector('#modalMatrixGradeDisplay').value = gradeValue;
    modalElement.querySelector('#matrixGradeHidden').value = gradeValue;
    modalElement.querySelector('#modalAssessmentDate').value = dateValue;

    const year = String(new Date(dateValue).getFullYear());
    const existingAssessment = allAssessmentsByYear[year];

    if (existingAssessment) {
        console.log("Mode Edit. Memuat data:", existingAssessment);

        modalElement.querySelector('#proposed_grade_modal').value = existingAssessment.proposed_grade || '';
        modalElement.querySelector('#priority_for_development_modal').value = existingAssessment.priority_for_development || 'No';
        
        competencyKeys.forEach(key => {
            const score = existingAssessment[key + '_score'] || '0';
            modalElement.querySelector(`#${key}_score`).value = score;
        });

    } else {
        console.log("Mode Input Baru.");
        
        modalElement.querySelector('#proposed_grade_modal').value = '';
        modalElement.querySelector('#priority_for_development_modal').value = '';
        
        modalElement.querySelectorAll('select[name*="_score"]').forEach(s => s.value = '0');
    }
    const modalInstance = new bootstrap.Modal(modalElement);
    modalInstance.show();
}

    
    initializePage();
});
</script>
@endpush
