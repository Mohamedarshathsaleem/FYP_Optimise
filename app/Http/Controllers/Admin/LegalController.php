<?php

namespace App\Http\Controllers\Admin;

use App\Models\Legal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class LegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (!auth()->user()->hasPermission('legals.view')) {
            abort(403, 'Unauthorized');
        }
        $legals = Legal::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.legals.index', compact('legals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.legals.create');
    }

    public function approvalIndex()
    {
        $legals = Legal::where('status_approval', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.legals.approval', compact('legals'));
    }
    
    public function approve($id)
    {
        $legal = Legal::findOrFail($id);
        $legal->update(['status_approval' => 'approved']);
        return redirect()->back()->with('success', 'Legal document approved!');
    }
    
    public function reject($id)
    {
        $legal = Legal::findOrFail($id);
        $legal->update(['status_approval' => 'rejected']);
        return redirect()->back()->with('success', 'Legal document rejected!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'authority' => 'required|string|max:255',
                'relevant_clause' => 'required|string|max:255',
                'reference_others' => 'required|string|max:255',
                'category' => 'required|in:Legal,Regulatory,Standard',
                'effective_date' => 'required|date',
                'relevant' => 'required|in:Y,N',
                'description' => 'required|string',
                'what_affected' => 'required|string|max:255',
                'action_required' => 'required|string',
                'responsible_person' => 'required|string|max:255',
                'last_review_date' => 'nullable|date',
                'review_frequency' => 'required|in:Monthly,Quarterly,Annually,Bi-annually',
                'further_action_bool' => 'required|in:Yes,No',
                'further_action' => 'nullable|string',
                'compliance_status' => 'required|in:Compliant,In Progress,Non-Compliant,Not Applicable',
                'evidence_compliance' => 'required|string',
                'remarks' => 'required|string'
            ]);

            Legal::create([
                'legal_id' => Legal::generateLegalId(),
                'title' => $request->title,
                'authority' => $request->authority,
                'relevant_clause' => $request->relevant_clause,
                'reference_others' => $request->reference_others,
                'category' => $request->category,
                'effective_date' => $request->effective_date,
                'relevant' => $request->relevant,
                'description' => $request->description,
                'what_affected' => $request->what_affected,
                'action_required' => $request->action_required,
                'responsible_person' => $request->responsible_person,
                'last_review_date' => $request->last_review_date,
                'review_frequency' => $request->review_frequency,
                'further_action_bool' => $request->further_action_bool,
                'further_action' => $request->further_action,
                'compliance_status' => $request->compliance_status,
                'evidence_compliance' => $request->evidence_compliance,
                'remarks' => $request->remarks
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Legal document created successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $legal = Legal::findOrFail($id);
            return response()->json($legal);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Legal document not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $legal = Legal::findOrFail($id);
            return response()->json($legal);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Legal document not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $legal = Legal::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'authority' => 'required|string|max:255',
                'relevant_clause' => 'required|string|max:255',
                'reference_others' => 'required|string|max:255',
                'category' => 'required|in:Legal,Regulatory,Standard',
                'effective_date' => 'required|date',
                'relevant' => 'required|in:Y,N',
                'description' => 'required|string',
                'what_affected' => 'required|string|max:255',
                'action_required' => 'required|string',
                'responsible_person' => 'required|string|max:255',
                'last_review_date' => 'nullable|date',
                'review_frequency' => 'required|in:Monthly,Quarterly,Annually,Bi-annually',
                'further_action_bool' => 'required|in:Yes,No',
                'further_action' => 'nullable|string',
                'compliance_status' => 'required|in:Compliant,In Progress,Non-Compliant,Not Applicable',
                'evidence_compliance' => 'required|string',
                'remarks' => 'required|string'
            ]);

            $legal->update($request->only([
                'title', 'authority', 'relevant_clause', 'reference_others', 'category',
                'effective_date', 'relevant', 'description', 'what_affected', 'action_required',
                'responsible_person', 'last_review_date', 'review_frequency', 'further_action_bool',
                'further_action', 'compliance_status', 'evidence_compliance', 'remarks'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Legal document updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $legal = Legal::findOrFail($id);
            $legal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Legal document deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get legal document statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Legal::count(),
            'relevant' => Legal::relevant()->count(),
            'compliant' => Legal::byComplianceStatus('Compliant')->count(),
            'in_progress' => Legal::byComplianceStatus('In Progress')->count(),
            'non_compliant' => Legal::byComplianceStatus('Non-Compliant')->count(),
            'requires_action' => Legal::requiresFurtherAction()->count(),
            'review_due' => Legal::reviewDue()->count(),
            'by_category' => [
                'legal' => Legal::byCategory('Legal')->count(),
                'regulatory' => Legal::byCategory('Regulatory')->count(),
                'standard' => Legal::byCategory('Standard')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get documents requiring review
     */
    public function reviewDue()
    {
        $documents = Legal::reviewDue()
                          ->orderBy('last_review_date', 'asc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
            'count' => $documents->count()
        ]);
    }

    /**
     * Generate compliance report
     */
    public function complianceReport()
    {
        $report = Legal::selectRaw('
                compliance_status,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM legals), 2) as percentage
            ')
            ->groupBy('compliance_status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
    public function detail($id)
    {
            $legal = Legal::with('activeLegalItems')->findOrFail($id);

            return view('admin.legals.detail', compact('legal'));

    }

    public function getItems($id)
{
    try {
        $legal = Legal::with('activeLegalItems')->findOrFail($id);

        // Generate sample items if none exist
        $items = $legal->activeLegalItems;

        if ($items->isEmpty()) {
            $items = $this->generateSampleItems($legal);
        }

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Legal document not found'
        ], 404);
    }
}

/**
 * Generate sample items based on legal document
 */
private function generateSampleItems($legal)
{
    $items = collect();

    if (strpos($legal->title, 'Energy Management') !== false) {
        $items = collect([
            (object)[
                'id' => 1,
                'item_id' => 'LR-EECA-001',
                'description' => 'Energy consumption monitoring requirements',
                'is_active' => true
            ],
            (object)[
                'id' => 2,
                'item_id' => 'LR-EECA-002',
                'description' => 'Energy efficiency reporting standards',
                'is_active' => true
            ],
            (object)[
                'id' => 3,
                'item_id' => 'LR-EECA-003',
                'description' => 'Energy management system implementation',
                'is_active' => true
            ],
            (object)[
                'id' => 4,
                'item_id' => 'LR-EECA-004',
                'description' => 'Energy audit compliance requirements',
                'is_active' => true
            ],
        ]);
    } elseif (strpos($legal->title, 'Renewable') !== false) {
        $items = collect([
            (object)[
                'id' => 5,
                'item_id' => 'LR-REA-001',
                'description' => 'Renewable energy adoption requirements',
                'is_active' => true
            ],
            (object)[
                'id' => 6,
                'item_id' => 'LR-REA-002',
                'description' => 'Solar energy installation standards',
                'is_active' => true
            ],
            (object)[
                'id' => 7,
                'item_id' => 'LR-REA-003',
                'description' => 'Green energy certification process',
                'is_active' => true
            ],
            (object)[
                'id' => 8,
                'item_id' => 'LR-REA-004',
                'description' => 'Renewable energy reporting obligations',
                'is_active' => true
            ],
        ]);
    }

    return $items;
}
    /**
     * Bulk update compliance status
     */
    public function bulkUpdateCompliance(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:legals,id',
            'compliance_status' => 'required|in:Compliant,In Progress,Non-Compliant,Not Applicable'
        ]);

        try {
            Legal::whereIn('id', $request->ids)
                 ->update(['compliance_status' => $request->compliance_status]);

            return response()->json([
                'success' => true,
                'message' => 'Compliance status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
