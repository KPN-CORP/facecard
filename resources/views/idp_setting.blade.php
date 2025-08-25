@extends('layouts.app')

@section('title', 'IDP Setting')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-primary">IDP Data Master</h1>

    {{-- Main Tab Navigation --}}
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link @if($activeTab === 'pills-dev-model') active @endif" id="pills-dev-model-tab" data-bs-toggle="pill" data-bs-target="#pills-dev-model" type="button" role="tab">Development Model</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link @if($activeTab === 'pills-competency') active @endif" id="pills-competency-tab" data-bs-toggle="pill" data-bs-target="#pills-competency" type="button" role="tab">Competency Names</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link @if($activeTab === 'pills-programs') active @endif" id="pills-programs-tab" data-bs-toggle="pill" data-bs-target="#pills-programs" type="button" role="tab">Development Programs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link @if($activeTab === 'pills-tools') active @endif" id="pills-tools-tab" data-bs-toggle="pill" data-bs-target="#pills-tools" type="button" role="tab">Review Tools</button>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    {{-- TAB 1: Development Model --}}
   <div class="tab-pane fade @if($activeTab === 'pills-dev-model') show active @endif" id="pills-dev-model" role="tabpanel">
        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Add Development Model</h5></div>
                    <div class="card-body">
                         @if ($errors->any())
                <div class="alert alert-primary">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                        <form action="{{ route('idp.setting.store') }}" method="POST" class="form-confirm">
                            @csrf
                            <div class="mb-3"><label for="model_name" class="form-label">Model Name</label><input type="text" name="name" id="model_name" class="form-control" placeholder="e.g., Assignment" required></div>
                            <div class="mb-4"><label for="percentage_slider" class="form-label">Percentage: <span id="slider_value" class="fw-bold text-primary">50%</span></label><input type="range" name="percentage" class="form-range" min="10" max="100" step="10" id="percentage_slider" value="50"></div>
                            <div class="text-end"><button type="submit" class="btn btn-primary">Add Model</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Existing Models</h5></div>
                    <div class="card-body">
                        <form action="{{ route('idp.setting.index') }}" method="GET">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center"><label for="model_per_page" class="form-label me-2 mb-0">Show</label><select name="model_per_page" id="model_per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();"><option value="10" @if(request('model_per_page', 10) == 10) selected @endif>10</option><option value="25" @if(request('model_per_page') == 25) selected @endif>25</option></select><span class="ms-2 text-muted">entries</span></div>
                                <div class="input-group" style="max-width: 300px;"><input type="search" name="model_search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('model_search') }}"><button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button></div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-custom-header">
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
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModelModal" data-id="{{ $model->id }}" data-name="{{ $model->name }}" data-percentage="{{ $model->percentage }}">Edit</button>
                                            <form action="{{ route('idp.setting.destroy', $model->id) }}" method="POST" class="d-inline form-delete">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-primary">Delete</button></form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center p-4">No models found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{!! $models->appends(request()->query())->links('vendor.pagination.custom') !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Competency Names --}}
    <div class="tab-pane fade @if($activeTab === 'pills-competency') show active @endif" id="pills-competency" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Master Data: Competency Names</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('idp.setting.index') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label for="cn_per_page" class="form-label me-2 mb-0">Show</label>
                            <select name="cn_per_page" id="cn_per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();">
                                <option value="10" @if(request('cn_per_page', 10) == 10) selected @endif>10</option>
                                <option value="25" @if(request('cn_per_page') == 25) selected @endif>25</option>
                            </select>
                            <span class="ms-2 text-muted">entries</span>
                        </div>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="search" name="cn_search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('cn_search') }}">
                            <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-custom-header">
                            <tr>
                                <th>No</th>
                                <th>Value</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                    <tbody>
                        @forelse($competencyNames as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($competencyNames->currentPage() - 1) * $competencyNames->perPage() }}</td>
                            <td>{{ $item->value }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editMasterModal" data-id="{{ $item->id }}" data-type="{{ $item->type }}" data-value="{{ $item->value }}">Edit</button>
                                <form action="{{ route('idp.setting.master.destroy', $item->id) }}" method="POST" class="d-inline form-delete-master">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">No data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-3">{!! $competencyNames->withQueryString()->links('vendor.pagination.custom') !!}</div>
            </div>
        </div>
    </div>
    
    {{-- TAB 3: Development Programs --}}
<div class="tab-pane fade @if($activeTab === 'pills-programs') show active @endif" id="pills-programs" role="tabpanel">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Master Data: Development Programs</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('idp.setting.index') }}" method="GET">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <label for="dp_per_page" class="form-label me-2 mb-0">Show</label>
                        <select name="dp_per_page" id="dp_per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();">
                            <option value="10" @if(request('dp_per_page', 10) == 10) selected @endif>10</option>
                            <option value="25" @if(request('dp_per_page') == 25) selected @endif>25</option>
                        </select>
                        <span class="ms-2 text-muted">entries</span>
                    </div>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="search" name="dp_search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('dp_search') }}">
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>

            {{-- Data Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th>No</th>
                            <th>Value</th>
                            <th class="text-center" style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($developmentPrograms as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($developmentPrograms->currentPage() - 1) * $developmentPrograms->perPage() }}</td>
                            <td>{{ $item->value }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editMasterModal" data-id="{{ $item->id }}" data-type="{{ $item->type }}" data-value="{{ $item->value }}">Edit</button>
                                <form action="{{ route('idp.setting.master.destroy', $item->id) }}" method="POST" class="d-inline form-delete-master">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">No data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-3">{!! $developmentPrograms->withQueryString()->links('vendor.pagination.custom') !!}</div>
            </div>
        </div>
    </div>
    
    
    {{-- TAB 4: Review Tools --}}
<div class="tab-pane fade @if($activeTab === 'pills-tools') show active @endif" id="pills-tools" role="tabpanel">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Master Data: Review Tools</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('idp.setting.index') }}" method="GET">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <label for="rt_per_page" class="form-label me-2 mb-0">Show</label>
                        <select name="rt_per_page" id="rt_per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit();">
                            <option value="10" @if(request('rt_per_page', 10) == 10) selected @endif>10</option>
                            <option value="25" @if(request('rt_per_page') == 25) selected @endif>25</option>
                        </select>
                        <span class="ms-2 text-muted">entries</span>
                    </div>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="search" name="rt_search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('rt_search') }}">
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th>No</th>
                            <th>Value</th>
                            <th class="text-center" style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviewTools as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($reviewTools->currentPage() - 1) * $reviewTools->perPage() }}</td>
                            <td>{{ $item->value }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editMasterModal" data-id="{{ $item->id }}" data-type="{{ $item->type }}" data-value="{{ $item->value }}">Edit</button>
                                <form action="{{ route('idp.setting.master.destroy', $item->id) }}" method="POST" class="d-inline form-delete-master">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">No data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {!! $reviewTools->withQueryString()->links('vendor.pagination.custom') !!}
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal Development Model --}}
<div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editModelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header"><h5 class="modal-title" id="editModelModalLabel">Edit Development Model</h5>...</div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_model_name" class="form-label">Model Name</label>
                        <input type="text" name="name" id="edit_model_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_percentage_slider" class="form-label">Percentage: <span id="edit_slider_value" class="fw-bold text-primary">50%</span></label>
                        <input type="range" name="percentage" class="form-range" min="10" max="100" step="10" id="edit_percentage_slider">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="replace_with_model" class="form-label">
                            Replace/Merge with Existing
                            <small class="text-muted d-block">(Optional: Move all related plans to another model and delete this one.)</small>
                        </label>
                        <select name="replace_with" id="replace_with_model" class="form-select">
                            <option value="">-- Don't replace, just update --</option>
                        </select>
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

{{-- Edit Modal Master Data --}}
<div class="modal fade" id="editMasterModal" tabindex="-1" aria-labelledby="editMasterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editMasterForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editMasterModalLabel">Edit Master Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_master_value" class="form-label">Value</label>
                        <input type="text" name="value" id="edit_master_value" class="form-control" required>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="replace_with" class="form-label">
                            Replace/Merge with Existing
                            <small class="text-muted">(Optional: Choose an option below to replace all occurrences of the old value with a new one)</small>
                        </label>
                        <select name="replace_with" id="replace_with" class="form-select">
                            <option value="">-- Don't replace, just update --</option>
                        </select>
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
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Slider "Add Model" ---
        const addSlider = document.getElementById('percentage_slider');
        const addSliderValue = document.getElementById('slider_value');
        if (addSlider) {
            addSlider.addEventListener('input', (event) => {
                addSliderValue.textContent = event.target.value + '%';
            });
        }

        const allModelsForModal = @json($allModelsForModal ?? []);

        // --- "Edit Model" ---
        const editModal = document.getElementById('editModelModal');
        if (editModal) {
            const editForm = document.getElementById('editModelForm');
            const editNameInput = document.getElementById('edit_model_name');
            const editSlider = document.getElementById('edit_percentage_slider');
            const editSliderValue = document.getElementById('edit_slider_value');
            const replaceSelect = document.getElementById('replace_with_model');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const percentage = button.getAttribute('data-percentage');

                let action = "{{ route('idp.setting.update', ['model' => ':id']) }}";
                editForm.action = action.replace(':id', id);

                editNameInput.value = name;
        editSlider.value = percentage;
        editSliderValue.textContent = percentage + '%';
        replaceSelect.innerHTML = '<option value="">-- Don\'t replace, just update --</option>';
        allModelsForModal.forEach(model => {
            if (model.id != id) { 
                const option = new Option(`${model.name} (${model.percentage}%)`, model.id);
                replaceSelect.add(option);
            }
        });
    });

    editSlider.addEventListener('input', (event) => {
        editSliderValue.textContent = event.target.value + '%';
    });
}
        
        // --- "Edit Master Data" ---
        const editMasterModal = document.getElementById('editMasterModal');
        if (editMasterModal) {
            const allMasterDataForModal = @json($allMasterDataForModal ?? []);
            const editMasterForm = document.getElementById('editMasterForm');
            const editMasterValueInput = document.getElementById('edit_master_value');
            const replaceSelect = document.getElementById('replace_with');

            editMasterModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const type = button.getAttribute('data-type');
                const value = button.getAttribute('data-value');

                let action = "{{ route('idp.setting.master.update', ['master' => ':id']) }}";
                editMasterForm.action = action.replace(':id', id);
                editMasterValueInput.value = value;
                
                replaceSelect.innerHTML = '<option value="">-- Don\'t replace, just update --</option>';
                
                if (allMasterDataForModal[type]) {
                    allMasterDataForModal[type].forEach(item => {
                        if (item.id != id) {
                            const option = new Option(item.value, item.id);
                            replaceSelect.add(option);
                        }
                    });
                }
            });
        }
        
        // --- Delete Confirmation ---
        document.querySelectorAll('.form-delete, .form-delete-master').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        const pillsTab = document.querySelector('#pills-tab');
        if (pillsTab) {
            const lastTabId = localStorage.getItem('lastActiveIdpSettingTab');
            if (lastTabId) {
                const tabToActivate = document.querySelector(`#pills-tab button[data-bs-target="${lastTabId}"]`);
                if(tabToActivate) {
                    const tab = new bootstrap.Tab(tabToActivate);
                    tab.show();
                }
            }
            pillsTab.addEventListener('shown.bs.tab', event => {
                localStorage.setItem('lastActiveIdpSettingTab', event.target.getAttribute('data-bs-target'));
            });
        }
    });
</script>
@endpush