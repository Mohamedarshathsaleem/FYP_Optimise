<?php

namespace App\Http\Controllers\Admin;

use App\Models\Committee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CommitteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        if (!auth()->user()->hasPermission('committees.view')) {
            abort(403, 'Unauthorized');
        }
        $committees = Committee::orderBy('role')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        return view('admin.committees.index', compact('committees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.committees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'role' => 'required|in:Chairperson,Secretary,Member',
                'department' => 'required|string|max:255',
                'communication_method' => 'required|string|max:255',
                'responsibilities' => 'required|string'
            ]);

            // Check if role already exists (only one Chairperson and Secretary allowed)
            if (in_array($request->role, ['Chairperson', 'Secretary'])) {
                $existingRole = Committee::where('role', $request->role)
                                       ->active()
                                       ->first();

                if ($existingRole) {
                    return response()->json([
                        'success' => false,
                        'message' => "An active {$request->role} already exists."
                    ], 422);
                }
            }

            Committee::create([
                'committee_id' => Committee::generateCommitteeId(),
                'name' => $request->name,
                'position' => $request->position,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'role' => $request->role,
                'department' => $request->department,
                'communication_method' => $request->communication_method,
                'responsibilities' => $request->responsibilities
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Committee member created successfully!'
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
            $committee = Committee::findOrFail($id);
            return response()->json($committee);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Committee member not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $committee = Committee::findOrFail($id);
            return response()->json($committee);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Committee member not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $committee = Committee::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'role' => 'required|in:Chairperson,Secretary,Member',
                'department' => 'required|string|max:255',
                'communication_method' => 'required|string|max:255',
                'responsibilities' => 'required|string'
            ]);

            // Check if role already exists (except current record)
            if (in_array($request->role, ['Chairperson', 'Secretary'])) {
                $existingRole = Committee::where('role', $request->role)
                                       ->where('id', '!=', $id)
                                       ->active()
                                       ->first();

                if ($existingRole) {
                    return response()->json([
                        'success' => false,
                        'message' => "An active {$request->role} already exists."
                    ], 422);
                }
            }

            $committee->update($request->only([
                'name', 'position', 'start_date', 'end_date', 'role',
                'department', 'communication_method', 'responsibilities'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Committee member updated successfully!'
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
            $committee = Committee::findOrFail($id);
            $committee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Committee member deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get committee statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Committee::count(),
            'active' => Committee::active()->count(),
            'chairperson' => Committee::byRole('Chairperson')->active()->count(),
            'secretary' => Committee::byRole('Secretary')->active()->count(),
            'members' => Committee::byRole('Member')->active()->count(),
            'departments' => Committee::select('department')->distinct()->count()
        ];

        return response()->json($stats);
    }

    /**
     * Generate appointment letter
     */
    public function appointmentLetter($id)
    {
        try {
            $committee = Committee::findOrFail($id);

            // Here you can implement PDF generation logic
            // For now, return committee data
            return response()->json([
                'success' => true,
                'data' => $committee,
                'message' => 'Appointment letter data retrieved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
