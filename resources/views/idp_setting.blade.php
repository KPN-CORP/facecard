@extends('layouts.app')

@section('title', 'IDP Setting')

@section('content')
<div class="container-fluid">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <h1 class="h3 mb-4 text-danger">IDP Setting</h1>

    <div class="row">
        {{-- Form to add new Development Model --}}
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Add Development Model</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('idp.setting.store') }}" method="POST" class="form-confirm">
                        @csrf
                        <div class="mb-3">
                            <label for="model_name" class="form-label">Model Name</label>
                            <input type="text" name="name" id="model_name" class="form-control" placeholder="e.g., Assignment" required>
                        </div>
                        <div class="mb-4">
                            <label for="percentage_slider" class="form-label">Percentage: <span id="slider_value" class="fw-bold text-danger">50%</span></label>
                            <input type="range" name="percentage" class="form-range" min="10" max="100" step="10" id="percentage_slider" value="50">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-danger">Add Model</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                 <div class="card-header bg-white">
                    <h5 class="mb-0">Existing Models</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Development Model Name</th>
                                    <th>Percentage</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($models as $model)
                                <tr>
                                    <td>{{ $model->name }}</td>
                                    <td>{{ $model->percentage }}%</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModelModal"
                                                data-id="{{ $model->id }}"
                                                data-name="{{ $model->name }}"
                                                data-percentage="{{ $model->percentage }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('idp.setting.destroy', $model->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center p-4">No models added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal Development Model --}}
<div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editModelForm" method="POST" class="form-confirm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModelModalLabel">Edit Development Model</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_model_name" class="form-label">Model Name</label>
                        <input type="text" name="name" id="edit_model_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_percentage_slider" class="form-label">Percentage: <span id="edit_slider_value" class="fw-bold text-danger">50%</span></label>
                        <input type="range" name="percentage" class="form-range" min="10" max="100" step="10" id="edit_percentage_slider">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script slider "Add Model"
        const addSlider = document.getElementById('percentage_slider');
        const addSliderValue = document.getElementById('slider_value');
        if (addSlider) {
            addSlider.addEventListener('input', (event) => {
                addSliderValue.textContent = event.target.value + '%';
            });
        }

        // Script modal "Edit Model"
        const editModal = document.getElementById('editModelModal');
        if (editModal) {
            const editForm = document.getElementById('editModelForm');
            const editNameInput = document.getElementById('edit_model_name');
            const editSlider = document.getElementById('edit_percentage_slider');
            const editSliderValue = document.getElementById('edit_slider_value');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const percentage = button.getAttribute('data-percentage');

                // Update action form
                let action = "{{ route('idp.setting.update', ['model' => ':id']) }}";
                editForm.action = action.replace(':id', id);

                // Update modal value
                editNameInput.value = name;
                editSlider.value = percentage;
                editSliderValue.textContent = percentage + '%';
            });

            editSlider.addEventListener('input', (event) => {
                editSliderValue.textContent = event.target.value + '%';
            });
        }
    });
</script>
@endpush
