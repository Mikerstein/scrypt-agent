<?php

return [

    'default' => env('AI_PROVIDER', 'groq'),

    'providers' => [

        'anthropic' => [
            'api_key'  => env('ANTHROPIC_API_KEY'),
            'model'    => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
            'base_url' => 'https://api.anthropic.com/v1',
        ],

        'openai' => [
            'api_key'  => env('OPENAI_API_KEY'),
            'model'    => env('OPENAI_MODEL', 'gpt-4o'),
            'base_url' => 'https://api.openai.com/v1',
        ],

        'groq' => [
            'api_key'  => env('GROQ_API_KEY'),
            'model'    => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
            'base_url' => 'https://api.groq.com/openai/v1',
        ],

        'gemini' => [
            'api_key'  => env('GEMINI_API_KEY'),
            'model'    => env('GEMINI_MODEL', 'gemini-2.0-flash'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ],

    ],

    'scrypt_context' => "You are the AI marketing engine for SCRYPT (scrypt.swiss),
    Switzerland's leading institutional-grade crypto execution platform headquartered in Zug.
    Key facts: \$25B+ trading volume, 300+ institutional clients, 40+ jurisdictions,
    FINMA & VQF licensed, founded 2020 by Norman Wooding (CEO) & Sylvan Martin (CGO),
    30+ person team, partners include OKX, Gauntlet, Braza Bank, Archax, BCB Group.
    Products: OTC trading, institutional custody (1100+ tokens), asset management
    (Crypto Growth, Bitcoin Growth, Nexus Yield strategies), DeFi access via Gauntlet, staking.
    Tone: institutional, sharp, authoritative. Never use hype, slang, or retail language.
    Focus on data, regulatory credibility, risk frameworks, and ROI.",


    'news_feeds' => [
    [
        'name'     => 'CoinDesk',
        'url'      => 'https://www.coindesk.com/arc/outboundfeeds/rss/',
        'category' => 'crypto_markets',
    ],
    [
        'name'     => 'The Block',
        'url'      => 'https://www.theblock.co/rss.xml',
        'category' => 'institutional',
    ],
    [
        'name'     => 'Decrypt',
        'url'      => 'https://decrypt.co/feed',
        'category' => 'crypto_markets',
    ],
    [
        'name'     => 'CryptoSlate',
        'url'      => 'https://cryptoslate.com/feed/',
        'category' => 'defi',
    ],
    [
        'name'     => 'Bitcoin Magazine',
        'url'      => 'https://bitcoinmagazine.com/feed',
        'category' => 'bitcoin',
    ],
],

'relevance_keywords' => [
    'institutional', 'DeFi', 'FINMA', 'MiCA', 'regulation', 'compliance',
    'custody', 'yield', 'stablecoin', 'RWA', 'tokenization', 'hedge fund',
    'asset manager', 'family office', 'OTC', 'trading volume', 'crypto bank',
    'Swiss', 'ETF', 'bitcoin', 'on-chain', 'Gauntlet', 'Morpho', 'USDC',
],

];
