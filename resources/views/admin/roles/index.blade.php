@extends('layouts.app')

@section('title', 'Role Setting')


@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="{{ asset('css/app.css') }}">



@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="h4 mb-4">Role Setting</h1>

            <ul class="nav nav-pills mb-4" id="mainRoleTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="create-role-tab" data-bs-toggle="pill" data-bs-target="#createRoleTab" type="button" role="tab" aria-controls="createRoleTab" aria-selected="true">Create Role</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="manage-role-tab" data-bs-toggle="pill" data-bs-target="#manageRoleTab" type="button" role="tab" aria-controls="manageRoleTab" aria-selected="false">Manage Role</button>
                </li>
            </ul>

            <div class="tab-content" id="mainRoleTabContent">
                <div class="tab-pane fade show active" id="createRoleTab" role="tabpanel" aria-labelledby="create-role-tab">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="card mb-4 border">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="role_name" class="form-label">Role Name *</label>
                                        <input type="text" name="role_name" id="role_name" class="form-control" placeholder="Role Name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="create_business_unit" class="form-label">Business Unit (Restricted View)</label>
                                        <select name="business_unit[]" id="create_business_unit" class="form-select" multiple>
                                            @foreach($filterData['businessUnits'] as $bu)<option value="{{ $bu->group_company }}">{{$bu->group_company}}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="create_company" class="form-label">Company (Restricted View)</label>
                                        <select name="company[]" id="create_company" class="form-select" multiple>
                                            @foreach($filterData['companies'] as $c)<option value="{{$c->company_name}}">{{$c->company_name}}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="create_location" class="form-label">Location (Restricted View)</label>
                                        <select name="location[]" id="create_location" class="form-select" multiple>
                                            @foreach($filterData['locations'] as $l)<option value="{{$l->office_area}}">{{$l->office_area}}</option>@endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border">
                            <div class="card-body p-4">
                                <h5 class="card-title h6">User Assignment</h5>
                                <select name="employee_ids[]" id="create_employee_ids" class="form-select" multiple="multiple">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->employee_id }}">{{ $employee->fullname }} ({{ $employee->employee_id }})</option>
                                    @endforeach
                                </select>
                                <div id="create-user-assignment-error" class="text-danger mt-2" style="font-size: 0.9em; min-height: 20px;"></div>
                            </div>
                        </div>

                        <div class="permissions-container d-flex">
                            <div class="nav flex-column nav-pills permissions-nav p-2" id="v-pills-tab-create" role="tablist" aria-orientation="vertical">
                                @foreach($permissions as $group => $sections)
                                    <button class="nav-link text-start @if($loop->first) active @endif" id="v-pills-create-{{ Str::slug($group) }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-create-{{ Str::slug($group) }}" type="button" role="tab">{{ $group }}</button>
                                @endforeach
                            </div>
                            <div class="tab-content p-4 flex-grow-1" id="v-pills-tabContent-create">
                                @foreach($permissions as $group => $sections)
                                    <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-create-{{ Str::slug($group) }}" role="tabpanel">
                                        @foreach($sections as $sectionName => $permissionList)
                                            <h6 class="text-uppercase text-muted fw-bold small mb-3 bg-light p-2 rounded">{{ $sectionName }}</h6>
                                            @foreach($permissionList as $permission)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="create_perm_{{ $permission->id }}">
                                                    <label class="form-check-label" for="create_perm_{{ $permission->id }}">{{ $permission->label }}</label>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="manageRoleTab" role="tabpanel" aria-labelledby="manage-role-tab">
                    <div class="card mb-4 border">
                        <div class="card-body p-4">
                            <label for="select_role_to_manage" class="form-label">Permission Role</label>
                            <select id="select_role_to_manage" class="form-select">
                                <option value="">Please select a role to manage...</option>
                                @foreach($roles as $role) <option value="{{ $role->id }}">{{ $role->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div id="manageRoleFormContainer" style="display: none;">
                        <form id="manageRoleForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card mb-4 border">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="edit_role_name" class="form-label">Role Name *</label>
                                            <input type="text" name="role_name" id="edit_role_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_business_unit" class="form-label">Business Unit (Restricted View)</label>
                                            <select name="business_unit[]" id="edit_business_unit" class="form-select" multiple>
                                                @foreach($filterData['businessUnits'] as $bu)<option value="{{ $bu->group_company }}">{{$bu->group_company}}</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_company" class="form-label">Company (Restricted View)</label>
                                            <select name="company[]" id="edit_company" class="form-select" multiple>
                                                @foreach($filterData['companies'] as $c)<option value="{{$c->company_name}}">{{$c->company_name}}</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_location" class="form-label">Location (Restricted View)</label>
                                            <select name="location[]" id="edit_location" class="form-select" multiple>
                                                @foreach($filterData['locations'] as $l)<option value="{{$l->office_area}}">{{$l->office_area}}</option>@endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4 border">
                                <div class="card-body p-4">
                                    <h5 class="card-title h6">User Assignment</h5>
                                        <select name="employee_ids[]" id="edit_employee_ids" class="form-select" multiple="multiple">
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->employee_id }}">{{ $employee->fullname }} ({{ $employee->employee_id }})</option>
                                            @endforeach
                                        </select>
                                     <div id="edit-user-assignment-error" class="text-danger mt-2" style="font-size: 0.9em; min-height: 20px;"></div>
                                 </div>
                             </div>
                            <div class="permissions-container d-flex">
                                <div class="nav flex-column nav-pills permissions-nav p-2" id="v-pills-tab-edit" role="tablist" aria-orientation="vertical">
                                    @foreach($permissions as $group => $sections)
                                        <button class="nav-link text-start @if($loop->first) active @endif" id="v-pills-edit-{{ Str::slug($group) }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-edit-{{ Str::slug($group) }}" type="button" role="tab">{{ $group }}</button>
                                    @endforeach
                                </div>
                                <div class="tab-content p-4 flex-grow-1" id="v-pills-tabContent-edit">
                                    @foreach($permissions as $group => $sections)
                                        <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-edit-{{ Str::slug($group) }}" role="tabpanel">
                                            @foreach($sections as $sectionName => $permissionList)
                                                <h6 class="text-uppercase text-muted fw-bold small mb-3 bg-light p-2 rounded">{{ $sectionName }}</h6>
                                                @foreach($permissionList as $permission)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="edit_perm_{{ $permission->id }}">
                                                        <label class="form-check-label" for="edit_perm_{{ $permission->id }}">{{ $permission->label }}</label>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-primary" id="deleteRoleButton">Delete Role</button>
                                <div>
                                    <button type="button" class="btn btn-light" onclick="app.hideManageForm()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Role</button>
                                </div>
                            </div>
                        </form>
                        <form id="deleteRoleForm" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    if (typeof window.app === 'undefined') { window.app = {}; }
    const allRolesData = @json($roles->keyBy('id'));
    const allEmployees = @json($employees->keyBy('employee_id'));
    const lockedEmployees = @json($lockedEmployees ?? []);

    app.hideManageForm = function() {
        document.getElementById('manageRoleFormContainer').style.display = 'none';
        document.getElementById('select_role_to_manage').value = '';
    }

    document.addEventListener("DOMContentLoaded", function() {
        const select2Config = {
            theme: "bootstrap-5",
            width: '100%',
            allowClear: true
        };

        const createSelect = $('#create_employee_ids').select2({...select2Config, placeholder: 'Search and select users...'});
        const editSelect = $('#edit_employee_ids').select2({...select2Config, placeholder: 'Search and select users...'});
        
        const createBusinessUnit = $('#create_business_unit').select2({...select2Config, placeholder: 'All Business Units'});
        const createCompany = $('#create_company').select2({...select2Config, placeholder: 'All Companies'});
        const createLocation = $('#create_location').select2({...select2Config, placeholder: 'All Locations'});
        
        const editBusinessUnit = $('#edit_business_unit').select2({...select2Config, placeholder: 'All Business Units'});
        const editCompany = $('#edit_company').select2({...select2Config, placeholder: 'All Companies'});
        const editLocation = $('#edit_location').select2({...select2Config, placeholder: 'All Locations'});

        // --- Function to show error role user (1 user 1 role) ---
        function attachRoleValidation(selectInstance, errorDivId) {
            selectInstance.on('select2:select', function(e) {
                const selectedEmployeeId = e.params.data.id;
                const errorDiv = document.getElementById(errorDivId);
                const currentRoleId = document.getElementById('select_role_to_manage')?.value;

                if (lockedEmployees.hasOwnProperty(selectedEmployeeId)) {
                    const roleData = allRolesData[currentRoleId];
                    const userRoles = lockedEmployees[selectedEmployeeId].split(' & ');
                    
                    if (!roleData || !userRoles.includes(roleData.name)) {
                        const employeeName = e.params.data.text.split(' (')[0];
                        errorDiv.textContent = `Error: ${employeeName} has already been assigned to: ${lockedEmployees[selectedEmployeeId]}.`;
                        
                        const currentValues = selectInstance.val();
                        currentValues.pop();
                        selectInstance.val(currentValues).trigger('change');

                        setTimeout(() => { errorDiv.textContent = ''; }, 5000);
                        return;
                    }
                }
                errorDiv.textContent = '';
            });
        }

        function populateManageForm() {
            const roleId = manageRoleSelect.value;
            if (!roleId) {
                manageFormContainer.style.display = 'none';
                return;
            }
            const roleData = allRolesData[roleId];
            
            manageForm.action = `{{ url('/admin/roles') }}/${roleData.id}`;
            manageForm.querySelector('#edit_role_name').value = roleData.name;

            editBusinessUnit.val(roleData.business_unit || []).trigger('change');
            editCompany.val(roleData.company || []).trigger('change');
            editLocation.val(roleData.location || []).trigger('change');
            
            const assignedEmployeeIds = roleData.users.map(user => user.employee?.employee_id).filter(id => id);
            editSelect.val(assignedEmployeeIds).trigger('change');

            manageForm.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
            const assignedPermissionIds = roleData.permissions.map(p => p.id);
            assignedPermissionIds.forEach(id => {
                const checkbox = manageForm.querySelector(`#edit_perm_${id}`);
                if (checkbox) checkbox.checked = true;
            });
            
            manageFormContainer.style.display = 'block';
        }

        // --- Event Listeners ---
        attachRoleValidation(createSelect, 'create-user-assignment-error');
        attachRoleValidation(editSelect, 'edit-user-assignment-error');
        
        const manageRoleSelect = document.getElementById('select_role_to_manage');
        const manageFormContainer = document.getElementById('manageRoleFormContainer');
        const manageForm = document.getElementById('manageRoleForm');
        const deleteButton = document.getElementById('deleteRoleButton');
        const createRoleForm = document.querySelector('form[action="{{ route('roles.store') }}"]');

        if(manageRoleSelect) manageRoleSelect.addEventListener('change', populateManageForm);

        // CREATE confirmation
        if(createRoleForm) {
            createRoleForm.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Create New Role?', icon: 'question', showCancelButton: true,
                    confirmButtonColor: '#AB2F2B', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Create!', cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) event.target.submit(); 
                });
            });
        }

        // UPDATE Confirmation
        if(manageForm) {
            manageForm.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Save Changes?', icon: 'question', showCancelButton: true,
                    confirmButtonColor: '#AB2F2B', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Save!', cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) event.target.submit(); 
                });
            });
        }

        // DELETE Confirmation
        if(deleteButton) {
            deleteButton.addEventListener('click', function() {
                const roleId = manageRoleSelect.value;
                if (!roleId) return alert('Please select a role to delete.');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You won't be able to revert the deletion of the "${allRolesData[roleId].name}" role!`,
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#AB2F2B', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!', cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteForm = document.getElementById('deleteRoleForm');
                        deleteForm.action = `{{ url('/admin/roles') }}/${roleId}`;
                        deleteForm.submit();
                    }
                });
            });
        }

        @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end', // Muncul di pojok kanan atas
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3500, // Hilang setelah 3.5 detik
            timerProgressBar: true
        });
    @endif
    
    // Menangkap session 'success_manage' khusus untuk halaman ini
    @if(session('success_manage'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success_manage') }}',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 5000, // Error ditampilkan sedikit lebih lama
            timerProgressBar: true
        });
    @endif
    });
</script>
@endpush