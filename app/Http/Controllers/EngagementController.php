<?php
namespace App\Http\Controllers;

use App\Models\SocialEngagement;
use App\Jobs\PublishEngagementJob;
use Illuminate\Http\Request;

class EngagementController extends Controller
{
    public function index()
    {
        $pending   = SocialEngagement::pendingReview()->latest()->get();
        $approved  = SocialEngagement::approved()->latest()->take(20)->get();
        $published = SocialEngagement::where('status','published')->latest()->take(20)->get();
        $rejected  = SocialEngagement::where('status','rejected')->latest()->take(20)->get();

        $stats = [
            'pending'   => SocialEngagement::pendingReview()->count(),
            'approved'  => SocialEngagement::approved()->count(),
            'published' => SocialEngagement::where('status','published')->count(),
            'rejected'  => SocialEngagement::where('status','rejected')->count(),
        ];

        return view('engagement.index',
            compact('pending','approved','published','rejected','stats'));
    }

    public function approve(Request $request, SocialEngagement $engagement)
    {
        $request->validate([
            'reply' => 'required|string|max:500',
        ]);

        $engagement->update([
            'status'          => 'approved',
            'generated_reply' => $request->reply,
        ]);

        // Dispatch publish job immediately
        PublishEngagementJob::dispatch($engagement->id);

        return back()->with('success',
            "Reply approved and queued for publishing to {$engagement->platform}.");
    }

    public function reject(Request $request, SocialEngagement $engagement)
    {
        $engagement->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Reply rejected.');
    }

    public function regenerate(Request $request, SocialEngagement $engagement)
    {
        $listener = new \App\Services\Social\SocialListeningService();
        $newReply = $listener->generateReply($engagement->toArray(),
            config('ai.default'));

        if ($newReply) {
            $engagement->update(['generated_reply' => $newReply]);
            return back()->with('success', 'Reply regenerated.');
        }

        return back()->with('error', 'Regeneration failed. Try again.');
    }
}