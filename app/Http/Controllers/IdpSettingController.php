<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DevelopmentModel;

class IdpSettingController extends Controller
{
    public function index()
    {
        $models = DevelopmentModel::orderBy('name')->get();
        return view('idp_setting', compact('models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:development_models,name',
            'percentage' => 'required|integer|min:10|max:100',
        ]);

        DevelopmentModel::create($validated);

        return redirect()->route('idp.setting.index')->with('success', 'Development Model berhasil ditambahkan!');
    }

    public function update(Request $request, DevelopmentModel $model)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:development_models,name,' . $model->id,
            'percentage' => 'required|integer|min:10|max:100',
        ]);

        $model->update($validated);

        return redirect()->route('idp.setting.index')->with('success', 'Development Model berhasil diupdate!');
    }

    public function destroy(DevelopmentModel $model)
    {
        $model->delete();
        return redirect()->route('idp.setting.index')->with('success', 'Development Model berhasil dihapus!');
    }
}
