<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stakeholder;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class StakeholderController extends Controller
{
    public function index()
    {
        $stakeholders = Stakeholder::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.stakeholders.index', compact('stakeholders'));
    }

    public function approvalIndex()
    {
        // Gunakan status 'pending' atau apapun status draft/baru stakeholder Anda
        $stakeholders = Stakeholder::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.stakeholders.approval', compact('stakeholders'));
    }
    
    public function approve($id)
    {
        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Stakeholder approved!');
    }
    
    public function reject($id)
    {
        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Stakeholder rejected!');
    }

    public function create()
    {
        return view('admin.stakeholders.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:Internal,External',
                'role' => 'required|string|max:255',
                'needs_expectations' => 'required|string',
                'influence_level' => 'required|in:Low,Medium,High',
                'communication_method' => 'required|string|max:255',
                'engagement_frequency' => 'required|string|max:255',
                'responsible_person' => 'required|string|max:255',
                'remarks' => 'required|string'
            ]);

            Stakeholder::create([
                'stakeholder_id' => Stakeholder::generateStakeholderId(),
                'name' => $request->name,
                'type' => $request->type,
                'role' => $request->role,
                'needs_expectations' => $request->needs_expectations,
                'influence_level' => $request->influence_level,
                'communication_method' => $request->communication_method,
                'engagement_frequency' => $request->engagement_frequency,
                'responsible_person' => $request->responsible_person,
                'remarks' => $request->remarks
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stakeholder created successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $stakeholder = Stakeholder::findOrFail($id);
            return response()->json($stakeholder);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stakeholder not found'
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $stakeholder = Stakeholder::findOrFail($id);
            return response()->json($stakeholder);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stakeholder not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $stakeholder = Stakeholder::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:Internal,External',
                'role' => 'required|string|max:255',
                'needs_expectations' => 'required|string',
                'influence_level' => 'required|in:Low,Medium,High',
                'communication_method' => 'required|string|max:255',
                'engagement_frequency' => 'required|string|max:255',
                'responsible_person' => 'required|string|max:255',
                'remarks' => 'required|string'
            ]);

            $stakeholder->update($request->only([
                'name', 'type', 'role', 'needs_expectations', 'influence_level',
                'communication_method', 'engagement_frequency', 'responsible_person', 'remarks'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Stakeholder updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $stakeholder = Stakeholder::findOrFail($id);
            $stakeholder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stakeholder deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
