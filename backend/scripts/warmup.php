<?php

require_once __DIR__ . '/../lib/logger.php';
$config = require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/cache-redis.php';
require_once __DIR__ . '/../lib/pncp-client.php';

echo "Iniciando Cache Warming...\n";

$cache = RedisCache::getInstance($config['redis']);
$client = new PNCPClient($config['pncp']['base_url']);

// Pré-carregar oportunidades nacionais
$data = $client->request('obterProcessosPoucaParticipacao', ['poucaParticipacao' => 'true']);
if ($data) {
    $cache->set("oportunidades:todos", $data, $config['cache_ttl']['oportunidades']);
    echo "Cache de oportunidades (Nacional) populado.\n";
}

// Pré-carregar alguns estados populares
foreach (['SP', 'RJ', 'MG', 'PR'] as $uf) {
    $data = $client->request('obterProcessosPoucaParticipacao', ['poucaParticipacao' => 'true', 'uf' => $uf]);
    if ($data) {
        $cache->set("oportunidades:{$uf}", $data, $config['cache_ttl']['oportunidades']);
        echo "Cache de oportunidades ({$uf}) populado.\n";
    }
}

echo "Warmup concluído!\n";
