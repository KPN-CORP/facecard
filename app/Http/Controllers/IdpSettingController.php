<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DevelopmentModel;
use App\Models\IndividualDevelopmentPlan;
use App\Models\DevelopmentPlanMaster;
use Illuminate\Support\Facades\DB;
use App\Rules\SumPercentageCheck;

class IdpSettingController extends Controller
{
     public function index(Request $request)
    {
    $modelPerPage = $request->input('model_per_page', 10);
    $modelsQuery = DevelopmentModel::query();
    if ($request->filled('model_search')) {
        $modelsQuery->where('name', 'like', '%' . $request->input('model_search') . '%');
    }
    $models = $modelsQuery->orderBy('name')->paginate($modelPerPage, ['*'], 'model_page');

    // --- Master Data Logic ---
    $cnPerPage = $request->input('cn_per_page', 10);
    $dpPerPage = $request->input('dp_per_page', 10);
    $rtPerPage = $request->input('rt_per_page', 10);

    // Competency Names
    $cnQuery = DevelopmentPlanMaster::where('type', 'competency_name');
    if ($request->filled('cn_search')) {
        $cnQuery->where('value', 'like', '%' . $request->input('cn_search') . '%');
    }
    $competencyNames = $cnQuery->orderBy('value', 'asc')->paginate($cnPerPage, ['*'], 'cn_page');

    // Development Programs
    $dpQuery = DevelopmentPlanMaster::where('type', 'development_program');
    if ($request->filled('dp_search')) {
        $dpQuery->where('value', 'like', '%' . $request->input('dp_search') . '%');
    }
    $developmentPrograms = $dpQuery->orderBy('value', 'asc')->paginate($dpPerPage, ['*'], 'dp_page');

    // Review Tools
    $rtQuery = DevelopmentPlanMaster::where('type', 'review_tools');
    if ($request->filled('rt_search')) {
        $rtQuery->where('value', 'like', '%' . $request->input('rt_search') . '%');
    }
    $reviewTools = $rtQuery->orderBy('value', 'asc')->paginate($rtPerPage, ['*'], 'rt_page');

    // Get ALL master data for the modal's dropdown
    $allModelsForModal = DevelopmentModel::orderBy('name')->get();
    $allMasterDataForModal = DevelopmentPlanMaster::all()->groupBy('type');
    
    $activeTab = 'pills-dev-model'; // Default tab
    if ($request->has('cn_page') || $request->has('cn_search')) {
        $activeTab = 'pills-competency';
    } elseif ($request->has('dp_page') || $request->has('dp_search')) {
        $activeTab = 'pills-programs';
    } elseif ($request->has('rt_page') || $request->has('rt_search')) {
        $activeTab = 'pills-tools';
    }

    return view('idp_setting', [
        'models' => $models,
        'allModelsForModal' => $allModelsForModal,
        'competencyNames' => $competencyNames,
        'developmentPrograms' => $developmentPrograms,
        'reviewTools' => $reviewTools,
        'allMasterDataForModal' => $allMasterDataForModal,
        'activeTab' => $activeTab, 
    ]);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:development_models,name',
            'percentage' => ['required', 'integer', 'min:1', new SumPercentageCheck()],
        ]);

        DevelopmentModel::create($validated);

        return redirect()->route('idp.setting.index')->with('success', 'Development Model added successfully.');
    }

    public function update(Request $request, DevelopmentModel $model)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:development_models,name,' . $model->id,
            'percentage' => ['required', 'integer', 'min:1', new SumPercentageCheck($model->id)],
            'replace_with' => 'nullable|integer|exists:development_models,id',
        ]);

        if ($request->filled('replace_with')) {
            $replacementModel = DevelopmentModel::find($validated['replace_with']);
            
            IndividualDevelopmentPlan::where('development_model_id', $model->id)
                ->update(['development_model_id' => $replacementModel->id]);
            
            $model->delete();
            
            return redirect()->route('idp.setting.index')->with('success', "'{$model->name}' has been successfully replaced with '{$replacementModel->name}'.");
        }

        $model->update($validated);
        return redirect()->route('idp.setting.index')->with('success', 'Development Model updated successfully.');
    }
    public function destroy(Request $request, DevelopmentModel $model)
    {
        $relatedPlansCount = IndividualDevelopmentPlan::where('development_model_id', $model->id)->count();

        if ($relatedPlansCount > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete '{$model->name}' because it is associated with {$relatedPlansCount} development plan(s). Please edit and replace it first.");
        }

        $model->delete();
        return redirect()->route('idp.setting.index', ['model_page' => $request->input('model_page', 1)])
                         ->with('success', 'Development Model has been deleted.');
    }

    public function updateMaster(Request $request, DevelopmentPlanMaster $master)
{
    $validated = $request->validate([
        'value' => 'required|string|max:255',
        'replace_with' => 'nullable|integer|exists:development_plan_master,id'
    ]);

    $oldValue = $master->value;
    $newValue = $validated['value'];

    if ($request->filled('replace_with')) {
        $replacementMaster = DevelopmentPlanMaster::find($validated['replace_with']);
        $newValue = $replacementMaster->value;

        IndividualDevelopmentPlan::where($master->type, $oldValue)->update([$master->type => $newValue]);
        
        $master->delete();
        
        return redirect()->back()->with('success', "'{$oldValue}' has been successfully replaced with '{$newValue}'.");
    }

    $master->update(['value' => $newValue]);
    
    IndividualDevelopmentPlan::where($master->type, $oldValue)->update([$master->type => $newValue]);

    return redirect()->back()->with('success', 'Master data updated successfully.');
}

public function destroyMaster(DevelopmentPlanMaster $master)
{
    $relatedPlansCount = IndividualDevelopmentPlan::where($master->type, $master->value)->count();
    if ($relatedPlansCount > 0) {
        return redirect()->back()
            ->with('error', "Cannot delete '{$master->value}' because it is associated with {$relatedPlansCount} development plan(s). Please edit and replace it first.");
    }

    $master->delete();
    
    return redirect()->back()->with('success', 'Master data deleted successfully.');
}
}