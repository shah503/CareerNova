<?php

return [
    'name' => 'Shahjee',
    'avatar' => '🧠',

    'ai_service' => env('AI_SERVICE', 'local'),

    'knowledge_base' => [
        'faq' => [
            [
                'question' => 'What is CareerNova?',
                'answer' => 'CareerNova is a professional MCQ testing platform for MDCAT, NTS, ETEA and competitive exams.',
                'keywords' => ['what', 'careernova', 'platform']
            ],
            [
                'question' => 'How do I start a test?',
                'answer' => 'Click "Start Test" from the dashboard to begin. Your session will be locked for security.',
                'keywords' => ['start', 'test', 'begin']
            ],
            [
                'question' => 'Can my answers be lost?',
                'answer' => 'No! We auto-save every 3 seconds. Your data is secure and encrypted.',
                'keywords' => ['save', 'lose', 'data']
            ],
            [
                'question' => 'How are tests graded?',
                'answer' => 'All answers are automatically graded. You get instant results with detailed explanations.',
                'keywords' => ['grade', 'mark', 'score']
            ],
        ],
        'tips' => [
            '📚 Take regular practice tests',
            '🎯 Focus on weak subjects first',
            '💡 Review explanations for wrong answers',
            '👥 Join study groups to learn from peers',
            '⏰ Practice time management',
        ]
    ]
];