<?php
// app/Http/Controllers/SwotAnalysisController.php

namespace App\Http\Controllers\Admin;

use App\Models\SwotAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class SwotAnalysisController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = SwotAnalysis::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('swot_id', 'like', "%{$search}%")
                      ->orWhere('strengths', 'like', "%{$search}%")
                      ->orWhere('weaknesses', 'like', "%{$search}%")
                      ->orWhere('opportunities', 'like', "%{$search}%")
                      ->orWhere('threats', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $swotAnalyses = $query->orderBy('created_at', 'desc')->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $swotAnalyses
                ]);
            }

            return view('admin.swot-analysis.index', compact('swotAnalyses'));

        } catch (\Exception $e) {
            \Log::error('SWOT index error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load data'
                ], 500);
            }

            // ❌ YANG LAMA (SALAH)
            // $swotAnalyses = collect()->paginate(10);

            // ✅ YANG BENAR - BUAT PAGINATION KOSONG MANUAL
            $swotAnalyses = $this->createEmptyPaginator(10);
            return view('admin.swot-analysis.index', compact('swotAnalyses'));
        }
    }

    /**
     * Create empty paginator manually
     */
    private function createEmptyPaginator($perPage = 10)
    {
        $page = Paginator::resolveCurrentPage() ?: 1;

        return new LengthAwarePaginator(
            collect([]), // Empty collection
            0, // Total items
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $swotAnalysis = SwotAnalysis::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $swotAnalysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SWOT Analysis not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'opportunities' => 'required|string',
            'threats' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['status'] = 'Active';
            $data['created_by'] = auth()->user()->name ?? 'System';

            $swotAnalysis = SwotAnalysis::create($data);

            return response()->json([
                'success' => true,
                'message' => 'SWOT Analysis created successfully',
                'data' => $swotAnalysis
            ]);
        } catch (\Exception $e) {
            \Log::error('SWOT store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SWOT Analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $swotAnalysis = SwotAnalysis::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'strengths' => 'required|string',
                'weaknesses' => 'required|string',
                'opportunities' => 'required|string',
                'threats' => 'required|string',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $swotAnalysis->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'SWOT Analysis updated successfully',
                'data' => $swotAnalysis->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('SWOT update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SWOT Analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $swotAnalysis = SwotAnalysis::findOrFail($id);
            $swotAnalysis->delete();

            return response()->json([
                'success' => true,
                'message' => 'SWOT Analysis deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('SWOT delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete SWOT Analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve SWOT Analysis
     */
    public function approve($id)
    {
        try {
            $swotAnalysis = SwotAnalysis::findOrFail($id);
            $swotAnalysis->update([
                'approved_by' => auth()->user()->name ?? 'System',
                'approved_at' => now(),
                'status' => 'Active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SWOT Analysis approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve SWOT Analysis'
            ], 500);
        }
    }

    /**
     * Archive SWOT Analysis
     */
    public function archive($id)
    {
        try {
            $swotAnalysis = SwotAnalysis::findOrFail($id);
            $swotAnalysis->update(['status' => 'Archived']);

            return response()->json([
                'success' => true,
                'message' => 'SWOT Analysis archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive SWOT Analysis'
            ], 500);
        }
    }
}
