<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotivationStrategy;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotivationStrategyController extends Controller
{
    public function index(): View
    {
        $motivations = MotivationStrategy::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.motivation-strategy.index', compact('motivations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motivation_activity'        => 'required|string|max:255',
            'target_group'               => 'required|string|max:255',
            'criteria_for_recognition'   => 'required|string|max:255',
            'recognition_method'         => 'required|string|max:255',
            'frequency'                  => 'required|in:Monthly,Quarterly,Annually,Bi-annually',
            'responsible_dept'           => 'required|string|max:255',
            'remarks'                    => 'nullable|string',
        ]);

        MotivationStrategy::create($request->all());

        return back()->with('success', 'Motivation strategy added.');
    }

    public function edit($id)
    {
        $strategy = MotivationStrategy::findOrFail($id);

        return response()->json($strategy);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'motivation_activity'        => 'required|string|max:255',
            'target_group'               => 'required|string|max:255',
            'criteria_for_recognition'   => 'required|string|max:255',
            'recognition_method'         => 'required|string|max:255',
            'frequency'                  => 'required|in:Monthly,Quarterly,Annually,Bi-annually',
            'responsible_dept'           => 'required|string|max:255',
            'remarks'                    => 'nullable|string',
        ]);

        $strategy = MotivationStrategy::findOrFail($id);
        $strategy->update($request->all());

        return back()->with('success', 'Motivation strategy updated.');
    }

    public function destroy($id)
    {
        $strategy = MotivationStrategy::findOrFail($id);
        $strategy->delete();

        return back()->with('success', 'Motivation strategy deleted.');
    }
}