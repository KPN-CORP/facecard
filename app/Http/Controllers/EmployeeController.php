<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Authenticate;
use App\Models\Employees;
use App\Models\CompetencyAssessment;
use App\Models\FormalEducation;
use App\Models\WorkExperience;
use App\Models\TrainingCertification;
use App\Models\PerformanceAppraisal;
use App\Models\ResultSummary;
use App\Models\MatrixGradeConfig;
use App\Models\IndividualDevelopmentPlan;
use App\Models\DevelopmentModel;
use App\Models\InternalMovement;
use App\Models\DevelopmentPlanMaster;
use App\Imports\DevelopmentPlanImport;
use App\Imports\CompetencyAssessmentImport;
use App\Imports\SingleEmployeeDevelopmentPlanImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ReportExport;
use App\Models\Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private function getActivePermissions()
    {
        $activeRole = Role::with('permissions')->first();

        return $activeRole ? $activeRole->permissions->pluck('name') : collect();
    }

public function index($employeeId = null, Request $request)
{
    $employee = $employeeId
        ? Employees::where('employee_id', $employeeId)->firstOrFail()
        : Employees::firstOrFail();

    $employeeId = $employee->employee_id;

    $internalMovements = \App\Models\InternalMovement::where('employee_id', $employeeId)
        ->orderBy('from_date', 'desc')
        ->get();

    // --- Pagination IDP Data ---
    $developmentModels = DevelopmentModel::all();
    $paginatedPlans = [];
    foreach ($developmentModels as $model) {
        $paginatedPlans[$model->id] = IndividualDevelopmentPlan::where('employee_id', $employeeId)
            ->where('development_model_id', $model->id)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'page_model_' . $model->id);
    }
    $uncategorizedPlans = IndividualDevelopmentPlan::where('employee_id', $employeeId)
        ->whereNull('development_model_id')
        ->orderBy('id', 'desc')
        ->paginate(5, ['*'], 'page_uncategorized');
    
    $allAssessments = CompetencyAssessment::where('employee_id', $employeeId)
        ->orderBy('period', 'desc')
        ->orderBy('updated_at', 'desc')
        ->get();

    $assessmentsForJs = $allAssessments->keyBy('period');

    $latestAssessment = $assessmentsForJs->first();
    
    if ($latestAssessment) {
        $needsRenewal = Carbon::parse($latestAssessment->updated_at)->diffInYears(now()) >= 2;
    } else {
        $needsRenewal = true;
    }
    
    $allMatrixGrades = MatrixGradeConfig::all()->groupBy(function ($item) {
        return (string) $item->period;
    });

    $uniqueGradeLevels = MatrixGradeConfig::select('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');
    $resultSummary = ResultSummary::where('employee_id', $employeeId)->first();
    
    $isIdpPaginationRequest = collect($request->keys())->some(function ($key) {
        return str_starts_with($key, 'page_model_') || $key === 'page_uncategorized';
    });

    $activeTab = $isIdpPaginationRequest ? 'idp' : 'facecard';

    return view('index', [ 
        'employee' => $employee,
        'formalEducations' => FormalEducation::where('employee_id', $employeeId)->orderBy('from_date', 'desc')->get(),
        'workExperiences' => WorkExperience::where('employee_id', $employeeId)->orderBy('from_date', 'desc')->get(),
        'trainings' => TrainingCertification::where('employee_id', $employeeId)->orderBy('start_date', 'desc')->get(),
        'performanceAppraisals' => PerformanceAppraisal::where('employee_id', $employeeId)->orderByDesc('appraisal_year')->get(),
        'resultSummary' => $resultSummary,
        'activePermissions' => $this->getActivePermissions(),
        'allMatrixGrades' => MatrixGradeConfig::all()->groupBy('period'),
        'latestAssessment' => $latestAssessment, 
        'assessmentsForJs' => $assessmentsForJs,
        'competencyNames' => array_keys(CompetencyAssessment::getCompetencyMap()),
        'needsRenewal' => $needsRenewal,
        'uniqueGradeLevels' => $uniqueGradeLevels, 
        'internalMovements' => $internalMovements,

        // IDP Variable Pagination
        'developmentModels' => $developmentModels,
        'paginatedPlans' => $paginatedPlans,
        'uncategorizedPlans' => $uncategorizedPlans,
        'activeTab' => $activeTab,
    ]);
}


    // Facecard list
    public function facecardList(Request $request)
    {
        $perPage = $request->input('per_page', default: 10);
        $search = $request->input('search');
        $query = Employees::query();

        $user = Auth::user();

        if ($user && $user->roles) {
        $role = $user->roles()->first(); 
        if ($role) {
            // Cek dan terapkan filter hanya jika ada isinya
            if (!empty($role->business_unit) && is_array($role->business_unit)) {
                $query->whereIn('group_company', $role->business_unit);
            }
            if (!empty($role->company) && is_array($role->company)) {
                $query->whereIn('company_name', $role->company);
            }
            if (!empty($role->location) && is_array($role->location)) {
                $query->whereIn('office_area', $role->location);
            }
        }
        
    }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('designation_name', 'like', "%{$search}%")
                  ->orWhere('job_level', 'like', "%{$search}%")
                  ->orWhere('group_company', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('fullname', 'asc')->paginate($perPage);

        $pageTitle = 'Facecard'; 
        if ($request->routeIs('idp.list')) {
            $pageTitle = 'Individual Development Plan';
        }

        return view('facecard_list', [
            'pageTitle' => $pageTitle, 
            'employees' => $employees,
            'activePermissions' => $this->getActivePermissions()
        ]);
    }



    public function idpList(Request $request)
    {
        return $this->facecardList($request);
    }


    /**
     * Save & Update Competency Assessment Data.
     */
    public function storeCompetencyAssessment(Request $request)
    {

    $validatedData = $request->validate([
        'employee_id' => 'required|string|max:25',
        'assessment_date' => 'required|date',
        'matrix_grade' => 'required|string|max:10',
        'proposed_grade' => 'nullable|string|max:10', 
        'priority_for_development' => 'required|string|in:Yes,No',
        'synergized_team_score' => 'nullable|integer|min:0|max:4',
        'integrity_score' => 'nullable|integer|min:0|max:4',
        'growth_score' => 'nullable|integer|min:0|max:4',
        'adaptive_score' => 'nullable|integer|min:0|max:4',
        'passion_score' => 'nullable|integer|min:0|max:4',
        'manage_planning_score' => 'nullable|integer|min:0|max:4',
        'decision_making_score' => 'nullable|integer|min:0|max:4',
        'relationship_building_score' => 'nullable|integer|min:0|max:4',
        'developing_others_score' => 'nullable|integer|min:0|max:4',
    ]);

    $dataToStore = $validatedData;
    $dataToStore['period'] = Carbon::parse($validatedData['assessment_date'])->year;

    CompetencyAssessment::updateOrCreate(
        [
            'employee_id' => $dataToStore['employee_id'],
            'period' => $dataToStore['period']
        ],
        $dataToStore
    );

    return redirect()->action([self::class, 'index'], ['employeeId' => $validatedData['employee_id']])
        ->with('success', 'Competency Assessment saved successfully!');
    }

    /**
     * Save & Update Result Summary Data.
     */
    public function storeResultSummary(Request $request)
    {

    $baseData = $request->validate([
        'employee_id' => 'required|string|exists:employees,employee_id',
        'form_type'   => 'required|string',
    ]);

        $dataToUpdate = [];
        if ($request->form_type === 'details_summary') {
            $validated = $request->validate([
                'proposed_grade' => 'nullable|string',
                'priority_for_development' => 'required|string',
            ]);
            $dataToUpdate = $validated;

        } elseif ($request->form_type === 'succession_summary') {
            $validated = $request->validate([
                'critical_position' => 'required|string',
                'successor_type' => 'nullable|string',
                'successor_to_position' => 'nullable|string',
            ]);
            $dataToUpdate = $validated;
        }

        if (!empty($dataToUpdate)) {
            ResultSummary::updateOrCreate(
                ['employee_id' => $request->employee_id],
                $dataToUpdate
            );
        }

        return redirect()->route('employee.profile', ['employeeId' => $request->employee_id])
                         ->with('success', 'Summary data has been saved successfully!');
    }
    


    public function showIdpPage($employeeId)
{
    $employee = \App\Models\Employees::where('employee_id', $employeeId)->firstOrFail();
    $developmentModels = \App\Models\DevelopmentModel::all();

    // 1. Get all unique, user-entered values from this employee's history
    $allEmployeePlans = \App\Models\IndividualDevelopmentPlan::where('employee_id', $employeeId)->get();
    $dbCompetencyNames = $allEmployeePlans->pluck('competency_name')->unique()->filter();
    $dbDevPrograms = $allEmployeePlans->pluck('development_program')->unique()->filter();
    $dbReviewTools = $allEmployeePlans->pluck('review_tools')->unique()->filter();

    // 2. Get the master list of options from the new database table
    $masterOptions = DevelopmentPlanMaster::all()->groupBy('type');
    $masterCompetencyNames = $masterOptions->get('competency_name', collect())->pluck('value');
    $masterDevPrograms = $masterOptions->get('development_program', collect())->pluck('value');
    $masterReviewTools = $masterOptions->get('review_tools', collect())->pluck('value');

    // 3. Merge the master list with the user's historical data to create the final dropdown options
    $competencyNameOptions = $masterOptions->get('competency_name', collect())->pluck('value')
        ->merge($dbCompetencyNames)->unique()->sort()->values();
        
    $developmentProgramOptions = $masterOptions->get('development_program', collect())->pluck('value')
        ->merge($dbDevPrograms)->unique()->sort()->values();
        
    $reviewToolOptions = $masterOptions->get('review_tools', collect())->pluck('value')
        ->merge($dbReviewTools)->unique()->sort()->values();

    // 4. Handle pagination (this logic remains the same)
    $paginatedPlans = [];
    foreach ($developmentModels as $model) {
        $paginatedPlans[$model->id] = \App\Models\IndividualDevelopmentPlan::where('employee_id', $employeeId)
            ->where('development_model_id', $model->id)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'page_model_' . $model->id);
    }
    $uncategorizedPlans = \App\Models\IndividualDevelopmentPlan::where('employee_id', $employeeId)
        ->whereNull('development_model_id')
        ->orderBy('id', 'desc')
        ->paginate(5, ['*'], 'page_uncategorized');

    // 5. Return the view with the new dynamic options
    return view('individual_dev_content', [ 
        'employee' => $employee,
        'paginatedPlans' => $paginatedPlans,
        'uncategorizedPlans' => $uncategorizedPlans,
        'developmentModels' => $developmentModels,
        'competencyNameOptions' => $competencyNameOptions,
        'developmentProgramOptions' => $developmentProgramOptions,
        'reviewToolOptions' => $reviewToolOptions,
    ]);
}

    /**
     * Save the newest IDP
     */
    public function storeDevelopmentPlan(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|string|exists:employees,employee_id',
            'development_model_id' => 'nullable|integer|exists:development_models,id',
            'competency_type' => 'required|string',
            'competency_name' => 'required|string',
            'review_tools' => 'nullable|string',
            'development_program' => 'required|string', 
            'expected_outcome' => 'required|string', 
            'time_frame_start' => 'required|date',
            'time_frame_end' => 'required|date|after_or_equal:time_frame_start',
            'realization_date' => 'nullable|date',
            'result_evidence' => 'nullable|string',
        ]);

        $validatedData['competency_name']     = Str::title($validatedData['competency_name']);
        $validatedData['development_program'] = Str::title($validatedData['development_program']);
        $validatedData['review_tools']        = Str::title($validatedData['review_tools']);

        $this->formatAndStoreMasterOption('competency_name', $validatedData['competency_name']);
        $this->formatAndStoreMasterOption('development_program', $validatedData['development_program']);
        $this->formatAndStoreMasterOption('review_tools', $validatedData['review_tools']);
    
        IndividualDevelopmentPlan::create($validatedData);
        return redirect()->back()->with('success', 'Development Plan added successfully!');
    }

    /**
     * Delete Individual Development Plan (IDP) Data.
     */
    public function destroyDevelopmentPlan(Request $request, IndividualDevelopmentPlan $idp)
{
    $employeeId = $idp->employee_id;
    $modelId = $idp->development_model_id;
    $isUncategorized = is_null($modelId);

    $pageKey = $isUncategorized ? 'page_uncategorized' : 'page_model_' . $modelId;

    $previousUrl = url()->previous();
    parse_str(parse_url($previousUrl, PHP_URL_QUERY), $query);
    $currentPage = $query[$pageKey] ?? 1;

    $idp->delete();

    $queryBuilder = IndividualDevelopmentPlan::where('employee_id', $employeeId);
    if ($isUncategorized) {
        $queryBuilder->whereNull('development_model_id');
    } else {
        $queryBuilder->where('development_model_id', $modelId);
    }
    $totalRemainingItems = $queryBuilder->count();

    $itemsPerPage = 5; 
    $lastPage = ceil($totalRemainingItems / $itemsPerPage);
    if ($lastPage < 1) {
        $lastPage = 1; 
    }

    $targetPage = ($currentPage > $lastPage) ? $lastPage : $currentPage;
    
    $redirectQuery = $request->query();
    $redirectQuery[$pageKey] = $targetPage;

    return redirect()->route('idp.show', ['employeeId' => $employeeId] + $redirectQuery)
                     ->with('success', 'Development Plan has been deleted.');
}

    public function updateDevelopmentPlan(Request $request, IndividualDevelopmentPlan $idp)
    {
        $validatedData = $request->validate([
            'development_model_id' => 'nullable|integer|exists:development_models,id',
            'competency_type' => 'required|string',
            'competency_name' => 'required|string',
            'review_tools' => 'nullable|string',
            'development_program' => 'required|string', 
            'expected_outcome' => 'required|string', 
            'time_frame_start' => 'required|date',
            'time_frame_end' => 'required|date|after_or_equal:time_frame_start',
            'realization_date' => 'nullable|date',
            'result_evidence' => 'nullable|string',
        ]);

        $validatedData['competency_name']     = Str::title($validatedData['competency_name']);
        $validatedData['development_program'] = Str::title($validatedData['development_program']);
        $validatedData['review_tools']        = Str::title($validatedData['review_tools']);

        $this->formatAndStoreMasterOption('competency_name', $validatedData['competency_name']);
        $this->formatAndStoreMasterOption('development_program', $validatedData['development_program']);
        $this->formatAndStoreMasterOption('review_tools', $validatedData['review_tools']);

        $idp->update($validatedData);
        return redirect()->back()->with('success', 'Development Plan updated successfully!');
    }

    private function formatAndStoreMasterOption(string $type, ?string $value): void
{
    if (empty($value)) {
        return;
    }
    
    $formattedValue = Str::title($value);

    DevelopmentPlanMaster::firstOrCreate(
        [
            'type' => $type,
            'value' => $formattedValue
        ]
    );
}


    public function importSingleDevelopmentPlan(Request $request)
{
    $request->validate([
        'employee_id' => 'required|string|exists:employees,employee_id',
        'idp_file' => 'required|file|mimes:xlsx,xls'
    ]);

    $importer = new SingleEmployeeDevelopmentPlanImport($request->employee_id);

    try {
        Excel::import($importer, $request->file('idp_file'));

        $successCount = $importer->successCount;

        if ($successCount > 0) {
            // if there's any data entry, success message will be shown
            return redirect()->back()->with('success', "{$successCount} development plans have been imported successfully!");
        } else {
            // if it isn't, validate the error
            if (!empty($importer->failures) && count($importer->failures) > 0) {
                $firstFailure = $importer->failures[0];
                $error = "Import failed. Row {$firstFailure->row()}: " . implode(', ', $firstFailure->errors());
                return redirect()->back()->with('error', $error);
            }
            return redirect()->back()->with('error', 'No valid data was found in the file to import.');
        }

    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $firstFailure = $failures[0];
        // to show message error more clearly
        $error = "Import failed. Row {$firstFailure->row()}: " . implode(', ', $firstFailure->errors());
        return redirect()->back()->with('error', $error);
        
    } catch (\Exception $e) {
        // another error handling 
        return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}

public function showReport(Request $request)
{
    $selectedYear = $request->input('year', now()->year);
    $searchQuery = $request->input('search');
    $perPage = $request->input('per_page', 10);
    $filters = $request->only(['business_unit', 'job_level', 'designation', 'talent_status', 'talent_box']);

    $query = Employees::query();

    $user = Auth::user();
    $role = $user->roles()->first(); 

    if ($role) {
        // parameter for main query
        if (!empty($role->business_unit) && is_array($role->business_unit)) {
            $query->whereIn('group_company', $role->business_unit);
        }
        if (!empty($role->company) && is_array($role->company)) {
            $query->whereIn('company_name', $role->company);
        }
        if (!empty($role->location) && is_array($role->location)) {
            $query->whereIn('office_area', $role->location);
        }
    }

    if (!empty($filters['business_unit'])) {
        $query->where('group_company', $filters['business_unit']);
    }
    if (!empty($filters['job_level'])) {
        $query->where('job_level', $filters['job_level']);
    }
    if (!empty($filters['designation'])) {
        $query->where('designation_name', $filters['designation']);
    }

    // Filter based on talent status, talent box, year
    if (!empty($filters['talent_status'])) {
        $query->whereHas('performanceAppraisals', function ($q) use ($selectedYear, $filters) {
            $q->where('appraisal_year', $selectedYear)
              ->where('talent_status', $filters['talent_status']);
        });
    }
    if (!empty($filters['talent_box'])) {
        $query->whereHas('performanceAppraisals', function ($q) use ($selectedYear, $filters) {
            $q->where('appraisal_year', $selectedYear)
              ->where('talent_box', $filters['talent_box']);
        });
    }

    $query->with([
        'performanceAppraisals' => function ($q) use ($selectedYear) {
            $q->where('appraisal_year', $selectedYear);
        },
        'developmentPlans' => function ($q) use ($selectedYear) {
            $q->whereYear('time_frame_end', $selectedYear);
        }
    ]);

    if ($searchQuery) {
        $query->where(function ($q) use ($searchQuery) {
            $q->where('fullname', 'like', "%{$searchQuery}%")
              ->orWhere('employee_id', 'like', "%{$searchQuery}%")
              ->orWhere('group_company', 'like', "%{$searchQuery}%") 
              ->orWhere('job_level', 'like', "%{$searchQuery}%")     
              ->orWhere('designation_name', 'like', "%{$searchQuery}%"); 
        });
    }

    $employees = $query->orderBy('fullname', 'asc')->paginate($perPage);

    foreach ($employees as $employee) {
        $appraisalForYear = $employee->performanceAppraisals->first();
        $employee->talent_status_for_year = $appraisalForYear->talent_status ?? 'N/A';
        $employee->talent_box_for_year = $appraisalForYear->talent_box ?? 'N/A';

        $plans = $employee->developmentPlans;
        $totalPlans = $plans->count();
        $completedPlans = $plans->filter(function ($plan) {
            return !empty($plan->result_evidence) && $plan->result_evidence !== '-';
        })->count();
        
        $employee->idp_progress = ($totalPlans > 0) ? "{$completedPlans}/{$totalPlans}" : "0/0";
    }

    $paYears = PerformanceAppraisal::select('appraisal_year')->distinct()->pluck('appraisal_year');
    
    $idpYears = IndividualDevelopmentPlan::select(DB::raw('YEAR(time_frame_end) as year'))
        ->whereNotNull('time_frame_end')
        ->distinct()
        ->pluck('year');

    $availableYears = $paYears->merge($idpYears)->unique()->sortDesc()->values();

    $buQuery = \App\Models\Employees::select('group_company')->whereNotNull('group_company')->distinct();
    $jobLevelQuery = \App\Models\Employees::select('job_level')->whereNotNull('job_level')->distinct();
    $designationQuery = \App\Models\Employees::select('designation_name')->whereNotNull('designation_name')->distinct();

    if ($role) {
        if (!empty($role->business_unit) && is_array($role->business_unit)) {
            $buQuery->whereIn('group_company', $role->business_unit);
        }
        if (!empty($role->company) && is_array($role->company)) {
            $jobLevelQuery->whereIn('company_name', $role->company);
            $designationQuery->whereIn('company_name', $role->company);
        }
        if (!empty($role->location) && is_array($role->location)) {
            $jobLevelQuery->whereIn('office_area', $role->location);
            $designationQuery->whereIn('office_area', $role->location);
        }
    }

    $filterOptions = [
        'businessUnits' => \App\Models\Employees::select('group_company')->whereNotNull('group_company')->distinct()->orderBy('group_company')->pluck('group_company'),
        'jobLevels'     => \App\Models\Employees::select('job_level')->whereNotNull('job_level')->distinct()->orderBy('job_level')->pluck('job_level'),
        'designations'  => \App\Models\Employees::select('designation_name')->whereNotNull('designation_name')->distinct()->orderBy('designation_name')->pluck('designation_name'),
        'talentStatuses'=> \App\Models\PerformanceAppraisal::select('talent_status')->whereNotNull('talent_status')->distinct()->orderBy('talent_status')->pluck('talent_status'),
        'talentBoxes'   => \App\Models\PerformanceAppraisal::select('talent_box')->whereNotNull('talent_box')->distinct()->orderBy('talent_box')->pluck('talent_box'),
    ];

    return view('report', [
        'employees' => $employees,
        'availableYears' => $availableYears,
        'selectedYear' => $selectedYear,
        'filterOptions' => $filterOptions,
        'activePermissions' => $this->getActivePermissions()
    ]);
}

public function downloadReport(Request $request)
{
    $request->validate([
        'report_name' => 'required|string|in:idp_progress,talent_report',
    ]);

    // 1. Take all the filter on showReport
    $selectedYear = $request->input('year');
    $filters = $request->only(['business_unit', 'job_level', 'designation', 'talent_status', 'talent_box']);
    $searchQuery = $request->input('search');
    $reportType = $request->report_name;

    // 2. build the identic query showReport
    $query = \App\Models\Employees::query();

    $user = Auth::user();
    if ($user && $user->roles) {
        $role = $user->roles()->first(); 
        if ($role) {
            // Terapkan batasan pada query utama
            if (!empty($role->business_unit) && is_array($role->business_unit)) {
                $query->whereIn('group_company', $role->business_unit);
            }
            if (!empty($role->company) && is_array($role->company)) {
                $query->whereIn('company_name', $role->company);
            }
            if (!empty($role->location) && is_array($role->location)) {
                $query->whereIn('office_area', $role->location);
            }
        }
    }

    if (!empty($filters['business_unit'])) {
        $query->where('group_company', $filters['business_unit']);
    }
    if (!empty($filters['job_level'])) {
        $query->where('job_level', $filters['job_level']);
    }
    if (!empty($filters['designation'])) {
        $query->where('designation_name', $filters['designation']);
    }
    if (!empty($filters['talent_status']) || !empty($filters['talent_box'])) {
        $query->whereHas('performanceAppraisals', function ($q) use ($selectedYear, $filters) {
            if ($selectedYear) {
                $q->where('appraisal_year', $selectedYear);
            }
            if (!empty($filters['talent_status'])) {
                $q->where('talent_status', $filters['talent_status']);
            }
            if (!empty($filters['talent_box'])) {
                $q->where('talent_box', $filters['talent_box']);
            }
        });
    }
    if ($searchQuery) {
        $query->where(function ($q) use ($searchQuery) {
            $q->where('fullname', 'like', "%{$searchQuery}%")
              ->orWhere('employee_id', 'like', "%{$searchQuery}%");
        });
    }

    // 3. Eager load relasi with 'year'
    $query->with([
        'performanceAppraisals' => fn($q) => $selectedYear ? $q->where('appraisal_year', $selectedYear) : $q->orderBy('appraisal_year', 'desc'),
        'developmentPlans' => fn($q) => $selectedYear ? $q->whereYear('time_frame_end', $selectedYear) : null,
    ]);

    // 4. Take all same data (without pagination)
    $employeesToExport = $query->orderBy('fullname', 'asc')->get();

    if ($employeesToExport->isEmpty()) {
        return back()->with('error', 'No data found matching your filter criteria to download.');
    }

    // 5. Data Process (IMPORTANT : Before doing the export)
    foreach ($employeesToExport as $employee) {
        $appraisalForYear = $employee->performanceAppraisals->first();
        $employee->talent_status_for_year = $appraisalForYear->talent_status ?? 'N/A';
        $employee->talent_box_for_year = $appraisalForYear->talent_box ?? 'N/A';
        
        $plans = $employee->developmentPlans;
        $totalPlans = $plans->count();
        $completedPlans = $plans->whereNotNull('result_evidence')->where('result_evidence', '!=', '-')->count();
        $employee->idp_progress = ($totalPlans > 0) ? "{$completedPlans}/{$totalPlans}" : "0/0";
    }

    // 6. Adding years into file name
    $yearString = $selectedYear ?: 'All-Years';
    $fileName = 'HC_Report_' . $reportType . '_' . $yearString . '_' . date('Y-m-d') . '.xlsx';
    
    // 7. Send data that have been process and year to Export class
    return Excel::download(new \App\Exports\ReportExport($employeesToExport, $reportType, $selectedYear), $fileName);
}
}