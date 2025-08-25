{{-- view-only, without edit & delete --}}

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
@endpush


<div class="container-fluid">
<div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 py-3">
            <h1 class="h5 mb-0 text-primary fw-bold text-center text-sm-start">Individual Development Plan</h1>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 p-3 border rounded bg-light gap-3">
                <div class="text-center text-md-start">
                    <h2 class="h5 mb-1 fw-bold">{{ $employee->fullname }}</h2>
                    <span class="text-muted">{{ $employee->employee_id }}</span>
                </div>
                <div class="text-center text-md-end">
                    <small class="text-muted">Publish Date</small>
                    <div class="fw-bold">
                        @if($latestIdp)
                            {{ \Carbon\Carbon::parse($latestIdp->created_at)->format('d F Y, H:i A') }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>

            @php
                $hasAnyData = collect($paginatedPlans)->some(fn($p) => $p && $p->isNotEmpty());
            @endphp

            @if(!$hasAnyData)
                <div class="text-center p-5 bg-light rounded">
                    <p class="mb-0">No Development Plan data available.</p>
                </div>
            @else
                @foreach($developmentModels as $model)
                    @php
                        $plansForModel = $paginatedPlans[$model->id] ?? null;
                    @endphp
                    @if($plansForModel && $plansForModel->isNotEmpty())
                        <div class="border rounded overflow-hidden mb-4">
                            <div class="d-flex flex-column flex-lg-row">
                                <div class="d-flex align-items-center justify-content-center text-center fw-semibold p-3 border-bottom border-lg-end border-lg-bottom-0 bg-success-subtle" style="flex-basis: 120px; flex-shrink: 0;">
                                    <span class="text-dark">{{ $model->name }} ({{ $model->percentage }}%)</span>
                                </div>
                                
                                <div class="flex-grow-1 bg-white" style="min-width: 0;">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered mb-0 align-middle small">
                                            <thead class="table-secondary text-center">
                                                <tr class="border-white">
                                                    <th class="align-middle border-white" colspan="3">Development Area</th>
                                                    <th class="align-middle border-white" rowspan="2">Development Program</th>
                                                    <th class="align-middle border-white" rowspan="2">Expected Outcome</th>
                                                    <th class="align-middle border-white" rowspan="2">Time Frame</th>
                                                    <th class="align-middle border-white" rowspan="2">Realization Date</th>
                                                    <th class="align-middle border-white" rowspan="2">Result Evidence</th>
                                                </tr>
                                                <tr class="border-white">
                                                     <th class="border-white">Competency Type</th>
                                                     <th class="border-white">Competency Name</th>
                                                     <th class="border-white">Review Tools</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($plansForModel as $plan)
                                                    <tr>
                                                        <td>{{ $plan->competency_type }}</td>
                                                        <td>{{ $plan->competency_name }}</td>
                                                        <td>{{ $plan->review_tools }}</td>
                                                        <td>{{ $plan->development_program }}</td>
                                                        <td>{{ $plan->expected_outcome }}</td>
                                                        <td class="text-nowrap text-center">{{ \Carbon\Carbon::parse($plan->time_frame_start)->format('M y') }} - {{ \Carbon\Carbon::parse($plan->time_frame_end)->format('M y') }}</td>
                                                        <td class="text-center">{{ $plan->realization_date ? \Carbon\Carbon::parse($plan->realization_date)->format('d M y') : '-' }}</td>
                                                        <td class="text-center">
                                                            @if(!empty($plan->result_evidence) && $plan->result_evidence !== '-')
                                                                <a href="{{ $plan->result_evidence }}" target="_blank" class="text-decoration-underline">Link</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($plansForModel->hasPages())
                                    <div class="p-2 border-top">
                                        {!! $plansForModel->withQueryString()->links('vendor.pagination.custom') !!}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
            
            <hr>
            <div class="row mt-4" style="font-size: 0.8rem;">
                <div class="col-lg-4 border-end pe-3">
                    <h6 class="fw-bold">70% – Learning by Doing (On-the-Job Learning)</h6>
                    <p class="text-muted mb-1">Examples of activities:</p>
                    <ul class="list-unstyled text-muted ps-3">
                        <li>- Handling new responsibilities or special projects</li>
                        <li>- Job rotation or enrichment</li>
                        <li>- Leading process improvement initiatives</li>
                    </ul>
                </div>
                <div class="col-lg-4 border-end px-3">
                    <h6 class="fw-bold">20% – Learning from Others (Social Learning)</h6>
                    <p class="text-muted mb-1">Examples of activities:</p>
                    <ul class="list-unstyled text-muted ps-3">
                        <li>- Coaching or mentoring with a supervisor or senior colleague</li>
                        <li>- Peer sharing, group discussions, or learning circles</li>
                        <li>- Shadowing experienced coworker</li>
                    </ul>
                </div>
                <div class="col-lg-4 ps-3">
                    <h6 class="fw-bold">10% – Formal Learning</h6>
                    <p class="text-muted mb-1">Examples of activities:</p>
                    <ul class="list-unstyled text-muted ps-3">
                        <li>- Attending training, workshops, or seminars</li>
                        <li>- Online courses or certification programs</li>
                        <li>- Reading books, journals, or e-learning modules</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>