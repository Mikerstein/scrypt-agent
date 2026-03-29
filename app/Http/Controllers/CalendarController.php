<?php
namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\ContentPillar;
use App\Models\ScheduledPost;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $pillars = ContentPillar::where('is_active', true)->get()->keyBy('day_of_week');

        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        $calendar = collect($days)->map(function ($day) use ($pillars) {
            $pillar = $pillars->get($day);

            $content = $pillar
                ? ContentItem::where('content_pillar_id', $pillar->id)
                    ->latest()->take(3)->get()
                : collect();

            return [
                'day'     => $day,
                'pillar'  => $pillar,
                'content' => $content,
                'today'   => now()->format('l') === $day,
            ];
        });

        $upcoming = ScheduledPost::with('contentItem')
            ->where('status', 'pending')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        return view('calendar', compact('calendar', 'upcoming'));
    }
}