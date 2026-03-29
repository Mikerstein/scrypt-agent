<?php
namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\ContentPillar;
use App\Services\AI\AIProviderFactory;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $items = ContentItem::with('pillar')->latest()->paginate(20);
        return view('content.index', compact('items'));
    }

    public function show(ContentItem $contentItem)
    {
        return view('content.show', compact('contentItem'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'pillar_id' => 'required|exists:content_pillars,id',
            'type'      => 'required|in:linkedin,twitter,email',
            'provider'  => 'required|in:groq,anthropic,openai,gemini',
            'segment'   => 'required|in:hedge_fund,bank,market_maker',
        ]);

        $pillar   = ContentPillar::findOrFail($request->pillar_id);
        $prompt   = $this->buildPrompt($pillar, $request->type, $request->segment);
        $ai       = AIProviderFactory::make($request->provider);
        $content  = $ai->generate($prompt, 1000);

        $item = ContentItem::create([
            'content_pillar_id' => $pillar->id,
            'type'              => $request->type,
            'ai_provider'       => $ai->getProviderName(),
            'ai_model'          => $ai->getModel(),
            'prompt_used'       => $prompt,
            'content'           => $content,
            'status'            => 'draft',
        ]);

        return redirect()->route('content.show', $item)
            ->with('success', 'Content generated successfully.');
    }

    public function updateStatus(Request $request, ContentItem $contentItem)
    {
        $request->validate(['status' => 'required|in:draft,approved,published,rejected']);
        $contentItem->update(['status' => $request->status]);
        return back()->with('success', 'Status updated to ' . $request->status);
    }

    private function buildPrompt(ContentPillar $pillar, string $type, string $segment): string
    {
        $segmentFocus = match($segment) {
            'hedge_fund'   => "Target Audience: Hedge Funds. Focus heavily on execution quality, tight spreads, DeFi yield strategies, and deep liquidity.",
            'bank'         => "Target Audience: Banks. Focus heavily on regulatory compliance (FINMA), white-labeling APIs, risk management, and security.",
            'market_maker' => "Target Audience: Market Makers. Focus heavily on zero-latency infrastructure, API stability, and robust connectivity.",
            default        => "Target Audience: Institutional Investors."
        };

        $pillarContext = "Content pillar: {$pillar->name}. {$pillar->description}. CTA: {$pillar->primary_cta}";

        return match($type) {
            'linkedin' => "Write a high-impact LinkedIn post for SCRYPT.
                {$segmentFocus}
                {$pillarContext}
                Requirements: Sharp hook in line 1. 3-4 paragraphs max. 1-2 real SCRYPT data points 
                (\$25B+ volume, 300+ clients, 40+ jurisdictions, FINMA/VQF licensed, Gauntlet partnership).
                End with CTA. Institutional tone. No emojis. No retail language. Max 280 words.",

            'twitter'  => "Write a 5-tweet thread for SCRYPT on X (Twitter).
                {$segmentFocus}
                {$pillarContext}
                Requirements: Tweet 1 is a bold hook. Tweets 2-4 are data-driven insights.
                Tweet 5 is the CTA. Each tweet under 280 chars. Max 2 hashtags total. Sharp, institutional tone.",

            'email'    => "Write an institutional email newsletter for SCRYPT.
                {$segmentFocus}
                {$pillarContext}
                Requirements: Include subject line. 400-500 words. 
                Structure: context → problem → SCRYPT solution → data → CTA.
                Authoritative tone. No fluff.",

            default    => "Write a LinkedIn post for SCRYPT about {$pillar->name}. {$pillarContext}",
        };
    }
    public function schedule(Request $request)
{
    $request->validate([
        'content_item_id' => 'required|exists:content_items,id',
        'platform'        => 'required|in:linkedin,twitter,email',
        'scheduled_at'    => 'required|date|after:now',
    ]);

    \App\Models\ScheduledPost::create([
        'content_item_id' => $request->content_item_id,
        'platform'        => $request->platform,
        'scheduled_at'    => $request->scheduled_at,
        'status'          => 'pending',
    ]);

    return back()->with('success', 'Post scheduled successfully.');
}
}