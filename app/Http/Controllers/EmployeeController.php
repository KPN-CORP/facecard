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
use App\Models\MovementTransaction; 
use App\Models\PromotionTransaction;
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
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    try {
        $employee = Employees::publicData()->where('employee_id', $employeeId)->firstOrFail();
        $employeeId = $employee->employee_id;

        $movements = MovementTransaction::where('employee_id', $employeeId)->get();
        $promotions = PromotionTransaction::where('employee_id', $employeeId)->get();
        
        $mergedData = collect();

        // --- proses movements
        foreach ($movements as $movement) {
            $key = $movement->form . '_' . $movement->to; 
            $types = [];

            if (strtolower($movement->is_promotion) === 'yes') $types[] = 'Promotion';
            if (strtolower($movement->is_demotion) === 'yes') $types[] = 'Demotion';

            $mergedData[$key] = (object) [
                'period_start'  => $movement->form,
                'period_end'    => $movement->to,
                'business_unit' => $movement->bu_name,
                'department'    => $movement->unit_name,
                'position'      => $movement->designation_name,
                'grade'         => null,
                'type'          => implode(', ', $types) ?: 'Transfer',
            ];
        }

        // --- proses promotions
        foreach ($promotions as $promotion) {
            $key = $promotion->form . '_' . $promotion->to;

            if ($mergedData->has($key)) {
                $mergedData[$key]->grade = $promotion->job_level;

                if ($mergedData[$key]->type === 'Movement' && strtolower($promotion->is_promotion) === 'yes') {
                    $mergedData[$key]->type = 'Promotion';
                }
            } else {
                $mergedData[$key] = (object) [
                    'period_start'  => $promotion->form,
                    'period_end'    => $promotion->to,
                    'business_unit' => null,
                    'department'    => null,
                    'position'      => null,
                    'grade'         => $promotion->job_level,
                    'type'          => strtolower($promotion->is_promotion) === 'yes' ? 'Promotion' : 'N/A',
                ];
            }
        }

        $internalMovements = $mergedData->sortByDesc('period_start');

        // --- Pagination IDP Data
        $developmentModels = DevelopmentModel::all();
        $paginatedPlans = [];
        foreach ($developmentModels as $model) {
            $paginatedPlans[$model->id] = IndividualDevelopmentPlan::where('employee_id', $employeeId)
                ->where('development_model_id', $model->id)
                ->orderBy('id', 'desc')
                ->paginate(5, ['*'], 'page_model_' . $model->id);
        }

        $allAssessments = CompetencyAssessment::where('employee_id', $employeeId)
            ->orderBy('period', 'desc')
            ->get();

        $assessmentsForJs = $allAssessments->keyBy('period');
        $latestAssessment = $allAssessments->first();

        if ($latestAssessment) {
            $lastAssessmentYear = (int) $latestAssessment->period;
            $currentYear = now()->year;
            $needsRenewal = ($currentYear - $lastAssessmentYear) >= 2;
        } else {
            $needsRenewal = true;
        }

        $allMatrixGrades = MatrixGradeConfig::all()->groupBy(function ($item) {
            return (string) $item->period;
        });

        $uniqueGradeLevels = MatrixGradeConfig::select('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        $resultSummary = ResultSummary::where('employee_id', $employeeId)->first();

        $isIdpPaginationRequest = collect($request->keys())->some(function ($key) {
            return str_starts_with($key, 'page_model_');
        });

        $latestIdp = IndividualDevelopmentPlan::where('employee_id', $employeeId)
                                              ->latest('created_at')
                                              ->first();

        $activeTab = $isIdpPaginationRequest ? 'idp' : 'facecard';

        $performanceAppraisals = PerformanceAppraisal::where('employee_id', $employeeId)->get();
        $trainings = TrainingCertification::where('employee_id', $employeeId)->get();

        $timestamps = [];

        if ($resultSummary) {
            $timestamps[] = $resultSummary->updated_at;
        }
        if ($performanceAppraisals->isNotEmpty()) {
            $timestamps[] = $performanceAppraisals->max('updated_at');
        }
        if ($latestAssessment) {
            $timestamps[] = $latestAssessment->updated_at;
        }
        if ($trainings->isNotEmpty()) {
            $timestamps[] = $trainings->max('updated_at');
        }

        $validTimestamps = collect($timestamps)->filter();
        $lastUpdatedTimestamp = $validTimestamps->isNotEmpty() ? $validTimestamps->max() : null;

        $uniqueJobLevels = Employees::select('job_level')
            ->whereNotNull('job_level')
            ->distinct()
            ->orderBy('job_level')
            ->pluck('job_level');

        return view('index', [ 
            'employee' => $employee,
            'formalEducations' => FormalEducation::where('employee_id', $employeeId)->orderBy('from_date', 'desc')->get(),
            'workExperiences' => WorkExperience::where('employee_id', $employeeId)->orderBy('from_date', 'desc')->get(),
            'trainings' => TrainingCertification::where('employee_id', $employeeId)->orderBy('start_date', 'desc')->get(),
            'performanceAppraisals' => PerformanceAppraisal::where('employee_id', $employeeId)->orderByDesc('appraisal_year')->get(),
            'resultSummary' => $resultSummary,
            'activePermissions' => $this->getActivePermissions(),
            'allMatrixGrades' => $allMatrixGrades,
            'latestAssessment' => $latestAssessment, 
            'assessmentsForJs' => $assessmentsForJs,
            'competencyNames' => array_keys(CompetencyAssessment::getCompetencyMap()),
            'needsRenewal' => $needsRenewal,
            'uniqueGradeLevels' => $uniqueGradeLevels, 
            'internalMovements' => $internalMovements,
            'uniqueJobLevels' => $uniqueJobLevels,
            'developmentModels' => $developmentModels,
            'paginatedPlans' => $paginatedPlans,
            'activeTab' => $activeTab,
            'latestIdp' => $latestIdp,
            'lastUpdatedTimestamp' => $lastUpdatedTimestamp,
        ]);

    } catch (ModelNotFoundException $e) {
        return redirect()->back()->withErrors(['message' => 'Employee not found.']);
    } catch (\Throwable $e) {
        // Bisa juga diarahkan ke error view
        return response()->view('facecard.list', [
            'message' => $e->getMessage()
        ], 500);
    }
}



    // Facecard list
    public function facecardList(Request $request)
    {
        $user = Auth::user();
        $query = Employees::query();
        $hasFilter = false;

        if ($user && $user->isManager()) {
            $query->where('manager_l1_id', $user->employee_id);
            $hasFilter = true;

        } elseif ($user && $user->roles) {
            $role = $user->roles()->first(); 
            if ($role) {
                if (!empty($role->business_unit) && is_array($role->business_unit)) {
                    $query->whereIn('group_company', $role->business_unit);
                }
                if (!empty($role->company) && is_array($role->company)) {
                    $query->whereIn('company_name', $role->company);
                }
                if (!empty($role->location) && is_array($role->location)) {
                    $query->whereIn('office_area', $role->location);
                }
                $hasFilter = true;
            }
        }

        // kalau tidak ada filter → kosongkan hasil
        $employees = $hasFilter 
            ? $query->orderBy('fullname', 'asc')->get()
            : collect();

        $pageTitle = $request->routeIs('idp.list')
            ? 'Individual Development Plan'
            : 'Facecard';

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
    $request->validate([
        'employee_id' => 'required|string',
    ]);

    // Hanya proses 'succession_summary', hapus blok 'details_summary'
    $validated = $request->validate([
        'critical_position' => 'nullable|string',
        'successor_type' => 'nullable|string',
        'successor_to_position' => 'nullable|string',
    ]);

    ResultSummary::updateOrCreate(
        ['employee_id' => $request->employee_id],
        $validated
    );

    return redirect()->route('employee.profile', ['employeeId' => $request->employee_id])
                     ->with('success', 'Succession summary has been saved successfully!');
}
    

    public function showIdpPage($employeeId)
{
    $employee = \App\Models\Employees::where('employee_id', $employeeId)->firstOrFail();
    $developmentModels = \App\Models\DevelopmentModel::all();
    $latestIdp = IndividualDevelopmentPlan::where('employee_id', $employeeId)
                                      ->latest('created_at')
                                      ->first();

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

     $allDevelopmentPrograms = DevelopmentPlanMaster::where('type', 'development_program')
        ->pluck('value')->unique()->sort()->values();

    $competencyMap = DevelopmentPlanMaster::where('type', 'development_program')
    ->whereNotNull('related_program') 
    ->get()
    ->groupBy('related_program')     
    ->map(function ($group) {
        return $group->pluck('value');
    });

    // 4. Handle pagination (this logic remains the same)
    $paginatedPlans = [];
    foreach ($developmentModels as $model) {
        $paginatedPlans[$model->id] = \App\Models\IndividualDevelopmentPlan::where('employee_id', $employeeId)
            ->where('development_model_id', $model->id)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'page_model_' . $model->id);
    }

    // 5. Return the view with the new dynamic options
    return view('individual_dev_content', [ 
        'employee' => $employee,
        'latestIdp' => $latestIdp,
        'paginatedPlans' => $paginatedPlans,
        'developmentModels' => $developmentModels,
        'competencyNameOptions' => $competencyNameOptions,
        'developmentProgramOptions' => $allDevelopmentPrograms,
        'reviewToolOptions' => $reviewToolOptions,
        'competencyMap' => $competencyMap,
    ]);
}

    /**
     * Save the newest IDP
     */
    public function storeDevelopmentPlan(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|string',
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

        $validatedData['competency_name'] = $this->getFormattedMasterValue(
        'competency_name', $validatedData['competency_name']
    );

    $validatedData['development_program'] = $this->getFormattedMasterValue(
        'development_program', $validatedData['development_program']
    );

    if (!empty($validatedData['review_tools'])) {
        $validatedData['review_tools'] = $this->getFormattedMasterValue(
            'review_tools', $validatedData['review_tools']
        );
    }

        $this->formatAndStoreMasterOption('competency_name', $validatedData['competency_name']);
    $this->formatAndStoreMasterOption('development_program', $validatedData['development_program']);
    if (!empty($validatedData['review_tools'])) {
        $this->formatAndStoreMasterOption('review_tools', $validatedData['review_tools']);
    }

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

    $pageKey = 'page_model_' . $modelId;

    $previousUrl = url()->previous();
    parse_str(parse_url($previousUrl, PHP_URL_QUERY), $query);
    $currentPage = $query[$pageKey] ?? 1;

    $idp->delete();

   $totalRemainingItems = IndividualDevelopmentPlan::where('employee_id', $employeeId)
                                                    ->where('development_model_id', $modelId)
                                                    ->count();

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

    private function getFormattedMasterValue(string $type, string $value): string
{
    // to check if the data from data master so it doesn't need formatting
    $exists = \App\Models\DevelopmentPlanMaster::where('type', $type)
        ->where('value', $value)
        ->exists();

    if ($exists) {
        return $value;
    }
    return \Illuminate\Support\Str::title($value);
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
        'employee_id' => 'required|string',
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
    $filters = $request->only(['business_unit', 'job_level', 'designation', 'unit', 'talent_status', 'talent_box']);

    $query = Employees::query();

    $user = Auth::user();
    $role = $user->roles()->first(); 

    if ($user && $user->isManager()) {
        $query->where('manager_l1_id', $user->employee_id);

    } elseif ($user && $user->roles) {
        $role = $user->roles()->first(); 
        if ($role) {
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
    if (!empty($filters['unit'])) {
        $query->where('unit', $filters['unit']);
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

    $employees = $query->orderBy('fullname', 'asc')->get();

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

    $unitQuery = \App\Models\Employees::select('unit')->whereNotNull('unit')->distinct();

    $filterOptions = [
        'businessUnits' => \App\Models\Employees::select('group_company')->whereNotNull('group_company')->distinct()->orderBy('group_company')->pluck('group_company'),
        'jobLevels'     => \App\Models\Employees::select('job_level')->whereNotNull('job_level')->distinct()->orderBy('job_level')->pluck('job_level'),
        'designations'  => \App\Models\Employees::select('designation_name')->whereNotNull('designation_name')->distinct()->orderBy('designation_name')->pluck('designation_name'),
        'talentStatuses'=> \App\Models\PerformanceAppraisal::select('talent_status')->whereNotNull('talent_status')->distinct()->orderBy('talent_status')->pluck('talent_status'),
        'talentBoxes'   => \App\Models\PerformanceAppraisal::select('talent_box')->whereNotNull('talent_box')->distinct()->orderBy('talent_box')->pluck('talent_box'),
        'units'         => $unitQuery->orderBy('unit')->pluck('unit'),
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
    $filters = $request->only(['business_unit', 'job_level', 'designation', 'unit', 'talent_status', 'talent_box']);
    $searchQuery = $request->input('search');
    $reportType = $request->report_name;

    // 2. build the identic query showReport
    $query = \App\Models\Employees::query();

    $user = Auth::user();
    if ($user && $user->roles) {
        $role = $user->roles()->first(); 
        if ($role) {
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
     if (!empty($filters['unit'])) {
        $query->where('unit', $filters['unit']);
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

    // 3. Eager load relation with 'year'
    $query->with([
        'performanceAppraisals' => fn($q) => $selectedYear ? $q->where('appraisal_year', $selectedYear) : $q->orderBy('appraisal_year', 'desc'),
        'developmentPlans' => fn($q) => $selectedYear ? $q->whereYear('time_frame_end', $selectedYear) : null,
    ]);

    // 4. Take all same data (without pagination)
    $employeesToExport = $query->orderBy('fullname', 'asc')->get();

    if ($employeesToExport->isEmpty()) {
        return back()->with('error', 'No data found matching your filter criteria to download.');
    }

    // 5. Process Data (IMPORTANT : Before doing the export)
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
public function downloadSingleIdpTemplate($employeeId)
    {
        // 1. Find employee data
        $employee = Employees::where('employee_id', $employeeId)->firstOrFail();

        // 2. Direct Path
        $templatePath = public_path('templates/template_IndividualDevelopmentPlan.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template file not found.');
        }

        // 3. Title Format
        $formattedName = Str::title($employee->fullname);
        $safeName = str_replace(' ', '_', $formattedName);
        $dynamicFilename = 'template_IndividualDevelopmentPlan_' . $safeName . '.xlsx';


        return response()->download($templatePath, $dynamicFilename);
    }
}