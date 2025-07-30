<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceAppraisal;

class PerformanceAppraisalController extends Controller
{
    public function update(Request $request, PerformanceAppraisal $appraisal)
    {
        $validated = $request->validate([
            'talent_status' => 'required|string|max:100',
            'talent_box' => 'required|string|max:100',
        ]);

        $appraisal->update($validated);

        return redirect()->back()->with('success', '9-Box data for ' . $appraisal->appraisal_year . ' has been updated!');
    }
}