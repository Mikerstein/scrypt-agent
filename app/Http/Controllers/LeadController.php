<?php
namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::withCount('activities')->latest()->paginate(20);
        $stats = [
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'meeting'   => Lead::where('status', 'meeting')->count(),
            'closed'    => Lead::where('status', 'closed')->count(),
            'lost'      => Lead::where('status', 'lost')->count(),
        ];
        return view('leads.index', compact('leads', 'stats'));
    }

    public function show(Lead $lead)
    {
        $lead->load('activities');
        return view('leads.show', compact('lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email'   => 'nullable|email',
            'title'   => 'nullable|string|max:255',
            'segment' => 'required|in:hedge_fund,bank,family_office,fintech,web3',
            'source'  => 'required|in:twitter,linkedin,email,referral',
        ]);

        Lead::create($request->all());
        return redirect()->route('leads.index')->with('success', 'Lead added successfully.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate(['status' => 'required|in:new,contacted,qualified,meeting,closed,lost']);
        $lead->update(['status' => $request->status]);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'type'        => 'status_change',
            'description' => 'Status updated to ' . $request->status,
            'occurred_at' => now(),
        ]);

        return back()->with('success', 'Lead status updated.');
    }

    public function addActivity(Request $request, Lead $lead)
    {
        $request->validate([
            'type'        => 'required|in:email_sent,call_booked,meeting_held,follow_up,note',
            'description' => 'required|string',
        ]);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'type'        => $request->type,
            'description' => $request->description,
            'occurred_at' => now(),
        ]);

        return back()->with('success', 'Activity logged.');
    }
}