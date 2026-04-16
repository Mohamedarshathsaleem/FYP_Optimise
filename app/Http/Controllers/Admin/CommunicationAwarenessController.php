<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationAwareness;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationAwarenessController extends Controller
{
    public function index()
    {
        $communications = CommunicationAwareness::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.communication_awareness.index', compact('communications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'action_initiative' => 'required|string|max:255',
            'type'              => 'required|in:Internal,External',
            'energy_message'    => 'required|string|max:255',
            'target_audience'   => 'required|in:All Employees,All office,Department head,Management',
            'communication'     => 'required|in:WhatsApp group,Email,PDF report',
            'person_in_charge'  => 'required|in:Energy Manager,Facility Supervisor,Compliance Offi,Data Analyst',
            'planned_date'      => 'required|date',
            'remarks'           => 'nullable|string',
        ]);

        CommunicationAwareness::create($request->all());

        return back()->with('success', 'Communication & awareness added.');
    }

    public function edit($id)
    {
        $communication = CommunicationAwareness::findOrFail($id);
        return response()->json($communication);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'action_initiative' => 'required|string|max:255',
            'type'              => 'required|in:Internal,External',
            'energy_message'    => 'required|string|max:255',
            'target_audience'   => 'required|in:All Employees,All office,Department head,Management',
            'communication'     => 'required|in:WhatsApp group,Email,PDF report',
            'person_in_charge'  => 'required|in:Energy Manager,Facility Supervisor,Compliance Offi,Data Analyst',
            'planned_date'      => 'required|date',
            'remarks'           => 'nullable|string',
        ]);

        $communication = CommunicationAwareness::findOrFail($id);
        $communication->update($request->all());

        return back()->with('success', 'Communication & awareness updated.');
    }

    public function destroy($id)
    {
        $communication = CommunicationAwareness::findOrFail($id);
        $communication->delete();

        return back()->with('success', 'Communication & awareness deleted.');
    }
}