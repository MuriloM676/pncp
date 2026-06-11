<?php

require_once __DIR__ . '/lib/logger.php';
$config = require_once __DIR__ . '/lib/config.php';
require_once __DIR__ . '/lib/cache-redis.php';
require_once __DIR__ . '/lib/rate-limit.php';
require_once __DIR__ . '/lib/pncp-client.php';
require_once __DIR__ . '/middleware/cors.php';

// Inicialização
$cache = RedisCache::getInstance($config['redis']);
$rateLimiter = new RateLimiter($cache, $config['rate_limit']['max'], $config['rate_limit']['window']);
$publicKey = getenv('PNCP_PUBLIC_KEY');
$client = new PNCPClient($config['pncp']['base_url'], $publicKey);
require_once __DIR__ . '/lib/compras-client.php';
$comprasClient = new ComprasGovClient($config['pncp']['base_url']);

// Rate Limit por IP
$ip = $_SERVER['REMOTE_ADDR'];
if (!$rateLimiter->check($ip)) {
    header('HTTP/1.1 429 Too Many Requests');
    echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
    exit;
}

// Roteamento Simples
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($requestUri, '/');

$endpointFile = __DIR__ . "/endpoints/{$path}.php";

header('Content-Type: application/json');

if (file_exists($endpointFile)) {
    try {
        include $endpointFile;
    } catch (Exception $e) {
        Logger::error("Erro no endpoint {$path}: " . $e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Internal server error.']);
    }
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Endpoint not found.']);
}
