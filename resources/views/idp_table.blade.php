{{-- resources/views/individual_dev_content.blade.php --}}


@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
    .idp-category-block {
        display: flex;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
        margin-bottom: 1rem;
    }

    .model-label-cell {
        flex: 0 0 10%; 
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: 600;
        font-size: 13px;
        padding: 0.5rem;
        border-right: 1px solid #dee2e6;
        word-break: break-word;
    }

    .model-label-cell.highlight-green {
        background-color: #d1e7dd;
    }

    .model-label-cell.highlight-gray {
        background-color: #e9ecef;
    }
.data-table-container {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .data-table-wrapper { overflow-x: auto; }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead th,
    .data-table tbody td {
        font-size: 13px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        text-align: left;
        background-color: #fff;
    }
    .data-table thead th {
        background-color: #ffffff;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    .data-table .action-cell { text-align: center; }
    .idp-table {
        table-layout: fixed;
        width: 100%;
        min-width: 1200px;
    }
    .idp-table th,
    .idp-table td {
        word-wrap: break-word;
    }
    
</style>
@endpush



    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 text-danger">Development Plan</h2>
            
        </div>
        <div class="card-body p-3">
            @php
                $hasAnyData = $uncategorizedPlans->isNotEmpty() || collect($paginatedPlans)->some(fn($p) => $p->isNotEmpty());
            @endphp

            @if(!$hasAnyData)
                <div class="text-center p-4">No Development Plan data available.</div>
            @else
                {{-- Loop untuk setiap Development Model --}}
                @foreach($developmentModels as $model)
                    @php
                        $plansForModel = $paginatedPlans[$model->id] ?? null;
                    @endphp
                    @if($plansForModel && $plansForModel->isNotEmpty())
                        <div class="idp-category-block">
                            <div class="model-label-cell highlight-green">
                                {{ $model->name }}<br>({{ $model->percentage }}%)
                            </div>
                            <div class="data-table-container">
                                <div class="data-table-wrapper">
                                    <table class="table table-hover data-table idp-table mb-0">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Development Area</th>
                                                <th colspan="2">Development Program</th>
                                                <th rowspan="2" style="width: 10%;">Time Frame</th>
                                                <th rowspan="2" style="width: 8%;">Realization</th>
                                                <th rowspan="2" style="width: 8%;">Evidence</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 8%;">Competency Type</th>
                                                <th style="width: 11%;">Competency Name</th>
                                                <th style="width: 10%;">Review Tools</th>
                                                <th style="width: 19%;">Development Program</th>
                                                <th style="width: 19%;">Expected Outcome</th>
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
                                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($plan->time_frame_start)->format('M y') }} - {{ \Carbon\Carbon::parse($plan->time_frame_end)->format('M y') }}</td>
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
                                {!! $plansForModel->withQueryString()->links('vendor.pagination.custom') !!}
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Uncategorized --}}
                @if($uncategorizedPlans->isNotEmpty())
                    <div class="idp-category-block">
                        <div class="model-label-cell highlight-gray">
                            Uncategorized
                        </div>
                        <div class="data-table-container">
                            <div class="data-table-wrapper">
                                <table class="table table-hover data-table idp-table mb-0">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Development Area</th>
                                            <th colspan="2">Development Program</th>
                                            <th rowspan="2" style="width: 10%;">Time Frame</th>
                                            <th rowspan="2" style="width: 8%;">Realization</th>
                                            <th rowspan="2" style="width: 8%;">Evidence</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 8%;">Competency Type</th>
                                            <th style="width: 11%;">Competency Name</th>
                                            <th style="width: 10%;">Review Tools</th>
                                            <th style="width: 19%;">Development Program</th>
                                            <th style="width: 19%;">Expected Outcome</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($uncategorizedPlans as $plan)
                                            <tr>
                                                <td>{{ $plan->competency_type }}</td>
                                                <td>{{ $plan->competency_name }}</td>
                                                <td>{{ $plan->review_tools }}</td>
                                                <td>{{ $plan->development_program }}</td>
                                                <td>{{ $plan->expected_outcome }}</td>
                                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($plan->time_frame_start)->format('M y') }} - {{ \Carbon\Carbon::parse($plan->time_frame_end)->format('M y') }}</td>
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
                            {!! $uncategorizedPlans->withQueryString()->links('vendor.pagination.custom') !!}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>