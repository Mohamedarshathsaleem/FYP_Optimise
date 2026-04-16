<?php
// app/Http/Controllers/RiskOpportunityController.php

namespace App\Http\Controllers\Admin;

use App\Models\RiskOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class RiskOpportunityController extends Controller
{

    public function index()
    {
        if (!auth()->user()->hasPermission('internal-external-issues.view')) {
        abort(403, 'Unauthorized');
    }
        // Create empty paginated collection
        $risks = RiskOpportunity::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.risks.index', compact('risks'));
    }
    
    public function approvalIndex()
    {
        // Permission untuk approve risk
        if (!auth()->user()->hasPermission('internal-external-issues.approval')) {
            abort(403, 'Unauthorized');
        }
        $risks = RiskOpportunity::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.risks.approval', compact('risks'));
    }
    
    public function approve($id)
    {
        if (!auth()->user()->hasPermission('internal-external-issues.approval')) {
            abort(403, 'Unauthorized');
        }
        $risk = RiskOpportunity::findOrFail($id);
        $risk->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Risk approved!');
    }
    
    public function reject($id)
    {
        if (!auth()->user()->hasPermission('internal-external-issues.approval')) {
            abort(403, 'Unauthorized');
        }
        $risk = RiskOpportunity::findOrFail($id);
        $risk->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Risk rejected!');
    }
    

    private function paginateCollection($collection, $perPage = 15, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $collection instanceof \Illuminate\Support\Collection ? $collection : collect($collection);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issue' => 'required|string|max:1000',
            'type' => 'required|in:Internal,External',
            'category' => 'required|in:Risk,Opportunity',
            'likelihood' => 'required|integer|min:1|max:5',
            'risk_level' => 'required|in:Low,Medium,High',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate risk_id
            $lastRisk = RiskOpportunity::orderBy('id', 'desc')->first();
            $number = $lastRisk ? (int) substr($lastRisk->risk_id, 2) + 1 : 1;
            $riskId = 'RO' . str_pad($number, 3, '0', STR_PAD_LEFT);

            // Simpan ke database
            $risk = RiskOpportunity::create([
                'risk_id' => $riskId,
                'issue' => $request->issue,
                'type' => $request->type,
                'category' => $request->category,
                'likelihood' => $request->likelihood,
                'risk_level' => $request->risk_level,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Risk/Opportunity created successfully',
                'data' => $risk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create risk/opportunity: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $risk = RiskOpportunity::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $risk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Risk/Opportunity not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $risk = RiskOpportunity::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'issue' => 'required|string|max:1000',
                'type' => 'required|in:Internal,External',
                'category' => 'required|in:Risk,Opportunity',
                'likelihood' => 'required|integer|min:1|max:5',
                'risk_level' => 'required|in:Low,Medium,High',
                'impact_description' => 'nullable|string|max:1000',
                'responsible_person' => 'nullable|string|max:255',
                'review_date' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $risk->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Risk/Opportunity updated successfully',
                'data' => $risk->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Risk update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update risk/opportunity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $risk = RiskOpportunity::findOrFail($id);
            $risk->delete();

            return response()->json([
                'success' => true,
                'message' => 'Risk/Opportunity deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Risk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete risk/opportunity: ' . $e->getMessage()
            ], 500);
        }
    }
}
