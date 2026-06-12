<?php

require_once __DIR__ . '/../lib/logger.php';
$config = require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/cache-redis.php';
require_once __DIR__ . '/../lib/compras-client.php';

echo "Iniciando Cache Warming (Dados Abertos)...\n";

$cache = RedisCache::getInstance($config['redis']);
$comprasClient = new ComprasGovClient($config['comprasgov']['base_url']);

$ttl = $config['cache_ttl']['oportunidades'];

// Pré-carregar oportunidades nacionais (Página 1)
$data = $comprasClient->getContratacoes(['pagina' => 1]);
if ($data && isset($data['resultado'])) {
    $cache->set("oportunidades:todos:todos:todos:todos:todos:todos:p1", $data, $ttl);
    echo "Cache de oportunidades (Nacional - p1) populado.\n";
}

// Pré-carregar estados populares (Página 1)
foreach (['SP', 'RJ', 'MG', 'PR'] as $uf) {
    $data = $comprasClient->getContratacoes(['uf' => $uf, 'pagina' => 1]);
    if ($data && isset($data['resultado'])) {
        $cache->set("oportunidades:{$uf}:todos:todos:todos:todos:todos:p1", $data, $ttl);
        echo "Cache de oportunidades ({$uf} - p1) populado.\n";
    }
}


echo "Warmup concluído!\n";
