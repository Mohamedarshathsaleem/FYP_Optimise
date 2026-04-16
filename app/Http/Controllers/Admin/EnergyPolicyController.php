<?php
// app/Http/Controllers/Admin/EnergyPolicyController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnergyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EnergyPolicyController extends Controller
{
    public function index()
    {
       
        if (!auth()->user()->hasPermission('energy-policy.view')) {
            abort(403, 'Unauthorized');
        }
        $energyPolicies = EnergyPolicy::latest()->get();
        $rejectedPolicies = EnergyPolicy::where('status', 'rejected')->latest()->get();

        return view('admin.energy-policy.index', compact('energyPolicies', 'rejectedPolicies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'policy_statement' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'energy_standard' => 'nullable|string|max:255',
            'policy_document' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:10240',
            'commitments' => 'nullable|array',
            'date_completed' => 'nullable|date',
            'date_approved' => 'nullable|date',
            'who_approved' => 'nullable|string|max:255'
        ]);

        try {
            $data = $request->except(['company_logo', 'policy_document']);

            // Create directories if they don't exist
            $logoDir = public_path('uploads/energy-policies/logos');
            $documentDir = public_path('uploads/energy-policies/documents');

            if (!File::exists($logoDir)) {
                File::makeDirectory($logoDir, 0755, true);
            }

            if (!File::exists($documentDir)) {
                File::makeDirectory($documentDir, 0755, true);
            }

            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                $logo = $request->file('company_logo');
                $logoName = time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                $logo->move($logoDir, $logoName);
                $data['company_logo'] = 'uploads/energy-policies/logos/' . $logoName;
            }

            // Handle policy document upload
            if ($request->hasFile('policy_document')) {
                $document = $request->file('policy_document');
                $documentName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
                $document->move($documentDir, $documentName);
                $data['document_path'] = 'uploads/energy-policies/documents/' . $documentName;
            }

            // Handle commitments as JSON
            if ($request->has('commitments')) {
                $data['commitments'] = json_encode($request->commitments);
            }

            // Set default values
            $data['summary'] = $request->summary ?? 'Energy policy with management commitments for sustainable operations.';
            $data['policy_completed'] = $request->has('policy_completed');
            $data['status'] = $request->status ?? 'draft';

            EnergyPolicy::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Energy policy created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create energy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $policy = EnergyPolicy::findOrFail($id);
        return view('admin.energy-policy.show', compact('policy'));
    }

    public function edit($id)
    {
        try {
            $policy = EnergyPolicy::findOrFail($id);

            // Decode commitments if it's JSON
            if ($policy->commitments && is_string($policy->commitments)) {
                $policy->commitments = json_decode($policy->commitments, true);
            }

            return response()->json($policy);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'policy_statement' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'energy_standard' => 'nullable|string|max:255',
            'policy_document' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:10240',
            'commitments' => 'nullable|array',
            'date_completed' => 'nullable|date',
            'date_approved' => 'nullable|date',
            'who_approved' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,approved,rejected'
        ]);

        try {
            $policy = EnergyPolicy::findOrFail($id);
            $data = $request->except(['company_logo', 'policy_document', '_method']);

            // Create directories if they don't exist
            $logoDir = public_path('uploads/energy-policies/logos');
            $documentDir = public_path('uploads/energy-policies/documents');

            if (!File::exists($logoDir)) {
                File::makeDirectory($logoDir, 0755, true);
            }

            if (!File::exists($documentDir)) {
                File::makeDirectory($documentDir, 0755, true);
            }

            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                // Delete old logo if exists
                if ($policy->company_logo && File::exists(public_path($policy->company_logo))) {
                    File::delete(public_path($policy->company_logo));
                }

                $logo = $request->file('company_logo');
                $logoName = time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                $logo->move($logoDir, $logoName);
                $data['company_logo'] = 'uploads/energy-policies/logos/' . $logoName;
            }

            // Handle policy document upload
            if ($request->hasFile('policy_document')) {
                // Delete old document if exists
                if ($policy->document_path && File::exists(public_path($policy->document_path))) {
                    File::delete(public_path($policy->document_path));
                }

                $document = $request->file('policy_document');
                $documentName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
                $document->move($documentDir, $documentName);
                $data['document_path'] = 'uploads/energy-policies/documents/' . $documentName;
            }

            // Handle commitments as JSON
            if ($request->has('commitments')) {
                $data['commitments'] = json_encode($request->commitments);
            }

            // Set policy completion status
            $data['policy_completed'] = $request->has('policy_completed');

            $policy->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Energy policy updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update energy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $policy = EnergyPolicy::findOrFail($id);

            // Delete associated files
            if ($policy->company_logo && File::exists(public_path($policy->company_logo))) {
                File::delete(public_path($policy->company_logo));
            }

            if ($policy->document_path && File::exists(public_path($policy->document_path))) {
                File::delete(public_path($policy->document_path));
            }

            $policy->delete();

            return response()->json([
                'success' => true,
                'message' => 'Energy policy deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete energy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $policy = EnergyPolicy::findOrFail($id);
            $policy->update([
                'status' => 'approved',
                'date_approved' => now(),
                'rejection_reason' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Energy policy approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve energy policy'
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        try {
            $policy = EnergyPolicy::findOrFail($id);
            $policy->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'date_approved' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Energy policy rejected successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject energy policy'
            ], 500);
        }
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,doc,docx,xlsx,xls|max:10240'
        ]);

        try {
            // Create directory if it doesn't exist
            $documentDir = public_path('uploads/energy-policies/documents');
            if (!File::exists($documentDir)) {
                File::makeDirectory($documentDir, 0755, true);
            }

            $document = $request->file('document');
            $documentName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
            $document->move($documentDir, $documentName);

            EnergyPolicy::create([
                'title' => $request->title,
                'document_path' => 'uploads/energy-policies/documents/' . $documentName,
                'summary' => 'Uploaded policy document',
                'status' => 'draft'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Energy policy document uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }
}
