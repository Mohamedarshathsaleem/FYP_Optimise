<?php
// app/Http/Controllers/Admin/EnergyTypeSettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnergyType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnergyTypeSettingsController extends Controller
{
    public function index(Request $request)
    {
        $query = EnergyType::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('conversion_coefficient', 'like', '%' . $request->search . '%');
        }

        $energyTypes = $query->paginate(10);

        return view('admin.energy-type-settings.index', compact('energyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:energy_types,name',
            'conversion_coefficient' => 'required|string|max:500'
        ]);

        EnergyType::create([
            'name' => $request->name,
            'conversion_coefficient' => $request->conversion_coefficient
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Energy type added successfully!'
        ]);
    }

    public function edit($id)
    {
        $energyType = EnergyType::findOrFail($id);
        return response()->json($energyType);
    }


    public function update(Request $request, $id)
    {
        $energyType = EnergyType::findOrFail($id);
    
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('energy_types')->ignore($id)],
            'conversion_coefficient' => 'required|string|max:500'
        ]);
    
        $energyType->update([
            'name' => $request->name,
            'conversion_coefficient' => $request->conversion_coefficient
        ]);
    
        return response()->json(['success' => true]);
    }

   public function destroy($id)
    {
        $energyType = EnergyType::findOrFail($id);
        $energyType->delete();
    
        return response()->json(['success' => true]);
    }

}
