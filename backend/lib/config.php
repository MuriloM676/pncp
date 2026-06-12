<?php

return [
    'pncp' => [
        'base_url' => getenv('PNCP_BASE_URL') ?: 'https://pncp.gov.br/api/pncp/',
    ],
    'comprasgov' => [
        'base_url' => getenv('COMPRASGOV_BASE_URL') ?: 'https://dadosabertos.compras.gov.br/',
    ],

    'redis' => [
        'host' => getenv('REDIS_HOST') ?: 'redis',
        'port' => getenv('REDIS_PORT') ?: 6379,
    ],
    'cache_ttl' => [
        'oportunidades' => (int)(getenv('CACHE_TTL_OPORTUNIDADES') ?: 300),
        'disputa' => (int)(getenv('CACHE_TTL_DISPUTA') ?: 15),
        'processo' => (int)(getenv('CACHE_TTL_PROCESSO') ?: 600),
        'fornecedor' => (int)(getenv('CACHE_TTL_FORNECEDOR') ?: 1800),
        'status' => (int)(getenv('CACHE_TTL_STATUS') ?: 60),
        'chat' => (int)(getenv('CACHE_TTL_CHAT') ?: 30),
    ],
    'rate_limit' => [
        'max' => (int)(getenv('RATE_LIMIT_MAX') ?: 60),
        'window' => (int)(getenv('RATE_LIMIT_WINDOW') ?: 60),
    ],
    'cors' => [
        'allowed_origins' => explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: 'http://localhost'),
    ]
];
