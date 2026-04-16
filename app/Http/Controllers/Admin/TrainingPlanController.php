<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;

class TrainingPlanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = TrainingPlan::latest();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('competency_area', 'like', "%{$search}%")
                  ->orWhere('target_group', 'like', "%{$search}%")
                  ->orWhere('training_method', 'like', "%{$search}%");
            });
        }
        
        $trainingPlans = $query->paginate(10)->appends($request->query());
        
        return view('admin.training-plans.index', compact('trainingPlans', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'competency_area' => 'required|string|max:255',
            'required_knowledge' => 'required|string',
            'target_group' => 'required|string|max:255',
            'competency_level' => 'required|in:1,2,1 to 4,1 to 5,2 to 5,3,4', // ← VALIDASI ENUM
            'training_needs' => 'required|string',
            'training_method' => 'required|string|max:255',
            'frequency' => 'required|string|max:100'
        ]);

        TrainingPlan::create($validated);

        return redirect()->route('admin.training-plans.index')
                        ->with('success', '✅ Training plan berhasil ditambahkan!');
    }

    public function destroy(TrainingPlan $trainingPlan)
    {
        $trainingPlan->delete();
        
        return redirect()->route('admin.training-plans.index')
                        ->with('success', '🗑️ Training plan berhasil dihapus!');
    }
}