<?php

return [
    'courses' => [
        'basics' => [
            'title' => 'Financial Markets Basics',
            'product_names' => ['Financial Markets Basics'],
            'offer_names' => ['Financial Markets Basics'],
            'external_references' => ['financial-markets-basics'],
            'included_by' => ['Foundation', 'Trader', 'Investor', 'Ultimate'],
        ],
        'fixed_income' => [
            'title' => 'Fixed Income Analysis & Investments',
            'product_names' => ['Fixed Income Analysis', 'Fixed Income Analysis & Investments', 'Fixed Income Analysis and Investments'],
            'offer_names' => ['Fixed Income Analysis & Investments', 'Fixed Income Analysis and Investments'],
            'external_references' => ['fixed-income', 'fixed-income-analysis-and-investments'],
            'included_by' => ['Foundation', 'Investor', 'Ultimate'],
        ],
        'equity' => [
            'title' => 'Equity Analysis & Investments',
            'product_names' => ['Equity Analysis & Investments', 'Equity Analysis and Investments'],
            'offer_names' => ['Equity Analysis & Investments', 'Equity Analysis and Investments'],
            'external_references' => ['equity-analysis', 'equity-analysis-and-investments'],
            'included_by' => ['Foundation', 'Trader', 'Investor', 'Ultimate'],
        ],
        'derivatives' => [
            'title' => 'Derivatives 101',
            'product_names' => ['Derivatives 101', 'Derivatives 101, Instruments, Valuation, Greeks and Strategies'],
            'offer_names' => ['Derivatives 101'],
            'external_references' => ['derivatives-101'],
            'included_by' => ['Investor', 'Ultimate'],
        ],
        'lmrss' => [
            'title' => 'LMRSS Day Trading System',
            'product_names' => ['LMRSS Day Trading', 'LMRSS Day Trading System'],
            'offer_names' => ['LMRSS Day Trading'],
            'external_references' => ['lmrss-day-trading'],
            'included_by' => ['Trader', 'Ultimate'],
        ],
        'live_room' => [
            'title' => 'OHC Trade Room Sessions',
            'product_names' => ['OHC Trade Room Sessions'],
            'offer_names' => ['OHC Trade Room Sessions'],
            'external_references' => ['ohc-trade-room-sessions'],
            'included_by' => ['Trader', 'Ultimate'],
        ],
        'advanced_derivatives' => [
            'title' => 'Advanced Derivatives',
            'product_names' => ['Advanced Derivatives'],
            'offer_names' => ['Advanced Derivatives'],
            'external_references' => ['advanced-derivatives'],
            'included_by' => ['Ultimate'],
        ],
        'mentorship' => [
            'title' => 'Group Mentorship',
            'product_names' => ['Group Mentorship'],
            'offer_names' => ['Group Mentorship'],
            'external_references' => ['group-mentorship'],
            'included_by' => ['Ultimate'],
        ],
    ],

    'bundles' => [
        'Foundation' => ['basics', 'equity', 'fixed_income'],
        'Trader' => ['basics', 'equity', 'lmrss', 'live_room'],
        'Investor' => ['basics', 'equity', 'fixed_income', 'derivatives'],
        'Ultimate' => ['basics', 'equity', 'fixed_income', 'derivatives', 'lmrss', 'live_room', 'advanced_derivatives', 'mentorship'],
    ],
];