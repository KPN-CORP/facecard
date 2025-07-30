<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Employees;
use App\Models\User;
use Illuminate\Support\Facades\DB; 

class RoleController extends Controller
{
    public function index()
    {
        $filterData = [
            'businessUnits' => Employees::select('group_company')->whereNotNull('group_company')->distinct()->orderBy('group_company')->get(),
            'companies' => Employees::select('company_name')->whereNotNull('company_name')->distinct()->orderBy('company_name')->get(),
            'locations' => Employees::select('office_area')->whereNotNull('office_area')->distinct()->orderBy('office_area')->get(),
        ];
        
        $employees = Employees::with('user.roles')->orderBy('fullname')->get();
        $permissions = Permission::all()->groupBy(['group', 'section']);
        $roles = Role::with('users.employee', 'permissions')->get();

        $activeRole = $roles->first();
        $activePermissions = $activeRole ? $activeRole->permissions->pluck('name') : collect();

        // function to make 1 user only 1 role
        $lockedEmployees = [];
        foreach ($employees as $employee) {
            if ($employee->user && $employee->user->roles->count() >= 1) {
                $lockedEmployees[$employee->employee_id] = $employee->user->roles->pluck('name')->join(' & ');
            }
        }

        return view('admin.roles.index', compact('filterData', 'employees', 'permissions', 'roles', 'activePermissions', 'lockedEmployees'));
    }

     public function filterEmployees(Request $request)
    {
        $query = \App\Models\Employees::query();

        // filter restricted on admin
        if ($request->filled('business_unit')) {
            $query->whereIn('group_company', $request->input('business_unit'));
        }
        if ($request->filled('company')) {
            $query->whereIn('company_name', $request->input('company'));
        }
        if ($request->filled('location')) {
            $query->whereIn('office_area', $request->input('location'));
        }

        $employees = $query->orderBy('fullname')->get(['employee_id', 'fullname']);

        return response()->json($employees);
    }

    /**
     * Save the newest data
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'role_name' => 'required|string|max:255|unique:roles,name',
        'business_unit' => 'nullable|array', 
        'company' => 'nullable|array',      
        'location' => 'nullable|array',
        'employee_ids' => 'nullable|array',
        'permissions' => 'nullable|array',
    ]);

    $role = Role::create([
        'name' => $validated['role_name'],
        'business_unit' => $validated['business_unit'] ?? null,
        'company' => $validated['company'] ?? null,
        'location' => $validated['location'] ?? null,
    ]);

    $role->permissions()->sync($request->input('permissions', []));

    $warningMessage = '';
        if (!empty($validated['employee_ids'])) {
            $selectedEmployeeIds = $validated['employee_ids'];
            
            // Find valid user based the chosen employee_id
            $validUsers = User::whereIn('employee_id', $selectedEmployeeIds)->get();
            $userIdsToSync = $validUsers->pluck('id');
            $role->users()->sync($userIdsToSync);

            // Find out employee that failed to save
            $syncedEmployeeIds = $validUsers->pluck('employee_id')->all();
            $failedEmployeeIds = array_diff($selectedEmployeeIds, $syncedEmployeeIds);

            if (!empty($failedEmployeeIds)) {
                $failedEmployeeNames = Employees::whereIn('employee_id', $failedEmployeeIds)->pluck('fullname')->join(', ');
                $warningMessage = ' However, the following users could not be assigned because they do not have a user account: ' . $failedEmployeeNames;
            }
        } else {
            $role->users()->sync([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role "' . $role->name . '" created successfully!' . $warningMessage);
    }


/**
 * Update role
 */
public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'business_unit' => 'nullable|array', 
            'company' => 'nullable|array',
            'location' => 'nullable|array',
            'employee_ids' => 'nullable|array',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $validated['role_name'],
            'business_unit' => $validated['business_unit'] ?? [],
            'company' => $validated['company'] ?? [],
            'location' => $validated['location'] ?? [],
        ]);

        $role->permissions()->sync($request->input('permissions', []));
        
        $warningMessage = '';
        if ($request->has('employee_ids')) {
            $selectedEmployeeIds = $request->input('employee_ids', []);
            
            $validUsers = User::whereIn('employee_id', $selectedEmployeeIds)->get();
            $userIdsToSync = $validUsers->pluck('id');
            $role->users()->sync($userIdsToSync);

            $syncedEmployeeIds = $validUsers->pluck('employee_id')->all();
            $failedEmployeeIds = array_diff($selectedEmployeeIds, $syncedEmployeeIds);
            
            if (!empty($failedEmployeeIds)) {
                $failedEmployeeNames = Employees::whereIn('employee_id', $failedEmployeeIds)->pluck('fullname')->join(', ');
                $warningMessage = ' However, the following users could not be assigned because they do not have a user account: ' . $failedEmployeeNames;
            }
        } else {
            $role->users()->sync([]);
        }

        return redirect()->route('roles.index')->with('success_manage', 'Role "' . $role->name . '" updated successfully!' . $warningMessage);
    }
    

    /**
     * Delete
     */
    public function destroy(Role $role)
    {
        $roleName = $role->name;
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();
        return redirect()->route('roles.index')->with('success_manage', 'Role "' . $roleName . '" has been deleted.');
    }
}