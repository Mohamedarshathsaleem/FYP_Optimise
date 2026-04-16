<?php

namespace App\Http\Controllers\Admin;

use App\Models\Scope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScopeController extends Controller
{
    public function index()
    {
        if(!auth()->user()->hasPermission('scope-boundaries.view')){
            abort(403, 'Unauthorized');
         }

        $scopes = Scope::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.scopes.index', compact('scopes'));
    }

    public function create()
    {
        return view('admin.scopes.create');
    }
    
    public function approvalIndex()
    {
        $scopes = Scope::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.scopes.approval', compact('scopes'));
    }
    
    public function approve($id)
    {
        $scope = Scope::findOrFail($id);
        $scope->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Scope approved!');
    }
    
    public function reject($id)
    {
        $scope = Scope::findOrFail($id);
        $scope->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Scope rejected!');
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'included' => 'required|string',
                'excluded' => 'required|string',
                'rationale_for_excluding' => 'required|string'
            ]);

            Scope::create([
                'scope_id' => Scope::generateScopeId(),
                'included' => $request->included,
                'excluded' => $request->excluded,
                'rationale_for_excluding' => $request->rationale_for_excluding
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scope created successfully!'
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
            $scope = Scope::findOrFail($id);
            return response()->json($scope);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Scope not found'
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $scope = Scope::findOrFail($id);
            return response()->json($scope);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Scope not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $scope = Scope::findOrFail($id);

            $request->validate([
                'included' => 'required|string',
                'excluded' => 'required|string',
                'rationale_for_excluding' => 'required|string'
            ]);

            $scope->update($request->only([
                'included', 'excluded', 'rationale_for_excluding'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Scope updated successfully!'
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
            $scope = Scope::findOrFail($id);
            $scope->delete();

            return response()->json([
                'success' => true,
                'message' => 'Scope deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
