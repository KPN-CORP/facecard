<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PerformanceAppraisalController;
use App\Http\Controllers\CompetencyAssessmentController;
use App\Http\Controllers\IdpSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/force-logout', [LoginController::class, 'logout'])->name('logout.force');


// --- Login ---
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Main Page
    Route::get('facecard', [EmployeeController::class, 'facecardList'])->name('facecard.list');
    Route::get('/employee/{employeeId?}', [EmployeeController::class, 'index'])->name('employee.profile');
    Route::put('/performance-appraisal/{appraisal}', [PerformanceAppraisalController::class, 'update'])->name('appraisal.update');
    Route::put('/competency-assessment/details/{assessment}', [CompetencyAssessmentController::class, 'updateDetails'])->name('assessment.updateDetails');
    
    // Report, Admin, & Import
    Route::get('report', [EmployeeController::class, 'showReport'])->name('report.show')->middleware('can:view_report_menu');
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('can:view_admin_setting');
    Route::get('import-center', [ImportController::class, 'index'])->name('import.index')->middleware('can:view_import_center');
    Route::delete('/import/delete/{log}', [ImportController::class, 'destroy'])->name('import.destroy');

    Route::post('/competency-assessment/store', [EmployeeController::class, 'storeCompetencyAssessment'])->name('competency.store');
    Route::post('/result-summary/store', [EmployeeController::class, 'storeResultSummary'])->name('resultSummary.store');
    
    // Individual Development Plan (IDP)
    Route::get('/idp', [EmployeeController::class, 'idpList'])->name('idp.list');
    Route::get('/idp/{employeeId}', [EmployeeController::class, 'showIdpPage'])->name('idp.show');
    Route::post('/idp/store', [EmployeeController::class, 'storeDevelopmentPlan'])->name('idp.store');
    Route::put('/idp/update/{idp}', [EmployeeController::class, 'updateDevelopmentPlan'])->name('idp.update');
    Route::delete('/idp/delete/{idp}', [EmployeeController::class, 'destroyDevelopmentPlan'])->name('idp.destroy');
    Route::post('/idp/import', [EmployeeController::class, 'importDevelopmentPlans'])->name('idp.import');
    Route::post('/idp/import-single', [EmployeeController::class, 'importSingleDevelopmentPlan'])->name('idp.import.single');
    Route::delete('/import/destroy-all', [ImportController::class, 'destroyAll'])->name('import.destroy_all'); 

    // IDP Setting -- IDP Data Master
    Route::get('/idp-setting', [IdpSettingController::class, 'index'])->name('idp.setting.index');
    Route::post('/idp-setting', [IdpSettingController::class, 'store'])->name('idp.setting.store');
    Route::put('/idp-setting/{model}', [IdpSettingController::class, 'update'])->name('idp.setting.update');
    Route::delete('/idp-setting/{model}', [IdpSettingController::class, 'destroy'])->name('idp.setting.destroy');
    Route::put('/idp-setting/master/{master}', [IdpSettingController::class, 'updateMaster'])->name('idp.setting.master.update');
    Route::delete('/idp-setting/master/{master}', [IdpSettingController::class, 'destroyMaster'])->name('idp.setting.master.destroy');

    
    // Role Setting
    Route::get('admin/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/admin/employees/filter', [RoleController::class, 'filterEmployees'])->name('roles.filter.employees');
    
    // Report & Import Actions
    Route::post('/report/download', [EmployeeController::class, 'downloadReport'])->name('report.download');
    Route::post('/import-center/process', [ImportController::class, 'processImport'])->name('import.process');
    Route::get('/import-download/{log}', [ImportController::class, 'downloadImportFile'])->name('import.download');
    Route::get('/test-employee/{id}', [ImportController::class, 'testEmployeeId']);
});