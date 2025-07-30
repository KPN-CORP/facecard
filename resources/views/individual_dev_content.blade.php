{{-- resources/views/individual_dev_content.blade.php --}}

@extends('layouts.app')

@section('title', 'IDP Management - ' . $employee->fullname)


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


@section('content')
<div class="container-fluid">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    
    @include('individual_dev_modal_input') 
    @include('idp_upload_modal')

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 text-danger">Development Plan</h2>
            <div class="actions">
                <button class="btn btn-outline-danger" onclick="app.openCreateIdpModal()"><i class="bi bi-plus-lg"></i> Input</button>
                <button class="btn btn-outline-primary" onclick="app.openModal('idpUploadModal')"><i class="bi bi-upload"></i> Upload</button>
            </div>
        </div>
        <div class="card-body p-3">

            <div class="mb-4">
        <a href="{{ route('idp.list') }}" class="btn-back">
            &laquo; Back to Individual Development Plan List
        </a>
    </div>

             {{-- Header Informasi Halaman --}}
<div class="d-flex justify-content-between align-items-start mb-4 p-3 border rounded bg-light">
    {{-- Detail Karyawan --}}
    <div>
        <h5 class="mb-1 fw-bold">{{ $employee->fullname }}</h5>
        <span class="text-muted">{{ $employee->employee_id }}</span>
    </div>
    {{-- Tanggal Saat Ini --}}
    <div class="text-end">
    <small class="text-muted">Current Date & Time</small>
    <div class="fw-bold">{{ now()->format('d F Y, H:i A') }}</div>
</div>
</div>
            @php
                $hasAnyData = $uncategorizedPlans->isNotEmpty() || collect($paginatedPlans)->some(fn($p) => $p->isNotEmpty());
            @endphp

            @if(!$hasAnyData)
                <div class="text-center p-4">No Development Plan data available.</div>
            @else
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
                                                <th rowspan="2" style="width: 7%;">Action</th>
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
                                                    <td class="action-cell text-center" style="width: 100px;">
                                                        <button class="btn btn-sm btn-outline-warning" title="Edit" onclick='app.openEditIdpModal({{ json_encode($plan) }})'>
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <form action="{{ route('idp.destroy', $plan->id) }}" method="POST" class="d-inline form-delete-idp">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
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

                {{--Uncategorized --}}
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
                                            <th rowspan="2" style="width: 7%;">Action</th>
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
                                                <td class="action-cell text-center" style="width: 100px;">
                                                    <button class="btn btn-sm btn-outline-warning" title="Edit" onclick='app.openEditIdpModal({{ json_encode($plan) }})'>
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="{{ route('idp.destroy', $plan->id) }}" method="POST" class="d-inline form-delete-idp">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
window.app = {};

document.addEventListener("DOMContentLoaded", function() {
    app.openModal = function(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        } else {
            console.error(`Modal with ID "${modalId}" not found.`);
        }
    };
    
    /**
     * New IDP Modal
     */
    app.openCreateIdpModal = function() {
        const form = document.getElementById('idpForm');
        if (!form) return;

        form.reset();
        form.action = "{{ route('idp.store') }}";
        document.getElementById('idpModalTitle').textContent = "Input Development Plan";
        document.getElementById('idpFormMethod').innerHTML = ""; 

        form.querySelectorAll('input, select, textarea').forEach(el => { 
            el.readOnly = false; 
            if (el.tagName === 'SELECT') el.disabled = false;
        });

        app.openModal('idpModal');
    };

    app.openEditIdpModal = function(plan) {
        Swal.fire({
            title: 'Edit Data?',
            text: "Anda akan mengubah data Development Plan ini.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#AB2F2B',
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
                    if(el.tagName === 'SELECT') el.disabled = false;
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
            confirmButtonColor: '#AB2F2B',
            cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    });

});
</script>
@endpush