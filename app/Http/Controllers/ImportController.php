<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ImportLog;
use App\Exports\FailedRowsExport;
use App\Exports\MissingHeaderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CompetencyAssessmentImport;
use App\Imports\DataMasterImport;
use App\Imports\DevelopmentPlanImport;
use App\Imports\TalentDataImport;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $query = ImportLog::latest('import_date');
        if ($request->filled('search')) {
            $query->where('result', 'like', '%' . $request->search . '%')
                  ->orWhere('data_type', 'like', '%' . $request->search . '%');
        }
        $logs = $query->paginate($request->input('per_page', 10));
        return view('import_center', compact('logs'));
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'import_type' => 'required|string',
            'import_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $type = $request->import_type;
        $file = $request->file('import_file');
        $originalPath = $file->store('imports/original');

        try {
            $importer = $this->getImporter($type);
        } catch (\Exception $e) {
            Storage::delete($originalPath);
            return back()->with('error', $e->getMessage());
        }

        try {
            Excel::import($importer, $originalPath);
        } catch (\Exception $e) {
            return $this->handleFatalImportError($e, $type, $originalPath);
        }

        $failures = method_exists($importer, 'failures') ? $importer->failures() : [];
        $successCount = property_exists($importer, 'successCount') ? $importer->successCount : 0;
        $failureCount = count($failures);

        if ($successCount === 0 && $failureCount > 0) {
            $this->handleImportFailureLog($failures, $type, $originalPath);
            return redirect()->back()->with('error', "Import failed. All {$failureCount} rows had errors. Please check the downloaded report.");
        }
        
        if ($successCount > 0 && $failureCount > 0) {
            $errorFilePath = $this->generateErrorFile($failures, $originalPath);
            $resultMessage = "Import completed with {$successCount} successful rows. However, {$failureCount} rows failed and were skipped.";
            
            ImportLog::create([
                'data_type' => $type, 'import_date' => now(), 'result' => $resultMessage,
                'status' => 'Success', 
                'original_file_path' => $originalPath, 'error_file_path' => $errorFilePath,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('import.index')->with('success', $resultMessage);
        }

        if ($successCount > 0 && $failureCount === 0) {
            $resultMessage = ucfirst(str_replace('_', ' ', $type)) . " data ({$successCount} rows) imported successfully.";
            ImportLog::create([
                'data_type' => $type, 'import_date' => now(), 'result' => $resultMessage,
                'status' => 'Success', 'original_file_path' => $originalPath,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('import.index')->with('success', $resultMessage);
        }

        Storage::delete($originalPath);
        return redirect()->back()->with('error', 'No valid data was found in the file to import.');
    }

    public function downloadImportFile(ImportLog $log)
    {
        $path = $log->error_file_path ?? $log->original_file_path;
        if ($path && Storage::exists($path)) {
            $fileName = !empty($log->error_file_path)
                ? 'error_report_' . basename($log->original_file_path)
                : 'original_' . basename($path);
            return Storage::download($path, $fileName);
        }
        return back()->with('error', 'File not found or is unavailable.');
    }
    
    public function destroy(ImportLog $log)
    {
        $previousUrl = url()->previous();
        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?: '', $queryParams);

        $currentPage = $queryParams['page'] ?? 1;
        $perPage = $queryParams['per_page'] ?? 10;
        $search = $queryParams['search'] ?? null;

    if ($log->original_file_path) Storage::delete($log->original_file_path);
    if ($log->error_file_path) Storage::delete($log->error_file_path);
    $log->delete();

    // count total item with same filter
    $query = ImportLog::query();
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('result', 'like', '%' . $search . '%')
              ->orWhere('data_type', 'like', '%' . $search . '%');
        });
    }
    $totalItems = $query->count();

    // logic to stay on the same page
    $lastPage = ceil($totalItems / $perPage);
    if ($lastPage < 1) {
        $lastPage = 1;
    }

    $targetPage = ($currentPage > $lastPage) ? $lastPage : $currentPage;
    $queryParams['page'] = $targetPage;

    return redirect()->route('import.index', $queryParams)
                     ->with('success', 'Import log has been deleted successfully.');
}

    public function destroyAll()
{
    $logs = \App\Models\ImportLog::all();

    foreach ($logs as $log) {
        if ($log->original_file_path && Storage::exists($log->original_file_path)) {
            Storage::delete($log->original_file_path);
        }
        if ($log->error_file_path && Storage::exists($log->error_file_path)) {
            Storage::delete($log->error_file_path);
        }
    }
    
    \App\Models\ImportLog::truncate();

    return redirect()->route('import.index')->with('success', 'All import history and files have been successfully deleted.');
}

    private function getImporter(string $type)
    {
        switch ($type) {
            case 'competency_assessment': return new CompetencyAssessmentImport();
            case 'data_master': return new DataMasterImport();
            case 'idp': return new DevelopmentPlanImport();
            case 'talent_box':
        case 'talent_status':
            return new \App\Imports\TalentDataImport($type);
        case 'internal_movement':
            return new \App\Imports\InternalMovementImport();
        case 'proposed_grade':
            return new \App\Imports\ProposedGradeImport();
        default: 
            throw new \Exception("Invalid import type '{$type}' specified.");
    }
    }

    private function handleFatalImportError(\Exception $e, string $type, string $originalPath)
    {
        $errorMessage = $e->getMessage();
        $userMessage = 'An unexpected error occurred during import: ' . $errorMessage;
        $errorFilePath = null;

        if (str_contains($errorMessage, 'Undefined array key')) {
            preg_match('/Undefined array key "(.*)"/', $errorMessage, $matches);
            $missingHeader = $matches[1] ?? 'unknown';
            $userMessage = "Header '{$missingHeader}' is missing from your file. Please correct the template and re-upload.";
            $errorFilePath = $this->generateErrorFile([], $originalPath, $userMessage);
        }

        ImportLog::create([
            'data_type' => $type, 'import_date' => now(), 'result' => $userMessage,
            'status' => 'Failed', 'original_file_path' => $originalPath,
            'error_file_path' => $errorFilePath, 'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('error', $userMessage);
    }

    private function handleImportFailureLog(array $failures, string $importType, string $originalPath): void
    {
        $errorFilePath = $this->generateErrorFile($failures, $originalPath);
        ImportLog::create([
            'data_type'          => $importType,
            'import_date'        => now(),
            'status'             => 'Failed',
            'result'             => count($failures) . ' rows failed to import. Check the downloaded report.',
            'original_file_path' => $originalPath,
            'error_file_path'    => $errorFilePath,
            'user_id'            => auth()->id(),
        ]);
    }

    private function generateErrorFile(array $failures, string $originalPath, string $customMessage = null): string
    {
        $errorRows = [];
        if ($customMessage) {
            $errorRows[] = ['row' => 'N/A', 'attribute' => 'General Error', 'error' => $customMessage, 'value' => 'N/A'];
        } else {
            foreach ($failures as $failure) {
                $errorRows[] = [
                    'row' => $failure->row(), 'attribute' => $failure->attribute(),
                    'error' => implode(', ', $failure->errors()), 'value' => json_encode($failure->values()),
                ];
            }
        }
        $errorFileName = 'errors_' . basename($originalPath);
        $errorFilePath = 'imports/errors/' . $errorFileName;
        Excel::store(new FailedRowsExport($errorRows), $errorFilePath);
        return $errorFilePath;
    }
}