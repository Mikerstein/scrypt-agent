<?php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateDailyContentJob;
use App\Jobs\PublishScheduledPostJob;
use App\Models\ScheduledPost;

// Scrape market news daily at 6am — one hour before content generation
Schedule::call(function () {
    \App\Jobs\ScrapeMarketNewsJob::dispatch(config('ai.default'));
})->dailyAt('06:00')->name('scrape-market-news')->withoutOverlapping();

// Generate content daily at 7am — picks today's pillar automatically
Schedule::call(function () {
    $day = now()->format('l'); // Monday, Tuesday...
    GenerateDailyContentJob::dispatch($day, config('ai.default'));
})->dailyAt('07:00')->name('generate-daily-content')->withoutOverlapping();

// Publish approved scheduled posts every 15 minutes
Schedule::call(function () {
    $duePosts = ScheduledPost::where('status', 'pending')
        ->where('scheduled_at', '<=', now())
        ->get();

    foreach ($duePosts as $post) {
        PublishScheduledPostJob::dispatch($post->id);
    }
})->everyFifteenMinutes()->name('publish-scheduled-posts')->withoutOverlapping();

// Weekly report every Sunday at 8pm
Schedule::command('scrypt:weekly-report --provider=groq')
    ->weeklyOn(0, '20:00')
    ->name('weekly-report')
    ->withoutOverlapping();

    // Engagement scan every 4 hours
Schedule::call(function () {
    \App\Jobs\AutonomousEngagementJob::dispatch(config('ai.default'));
})->everySixHours()->name('autonomous-engagement')->withoutOverlapping();