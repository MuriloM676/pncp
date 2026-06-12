<?php

$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';

if (!$cnpj || !$ano || !$sequencial) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj, ano and sequencial are required.']);
    exit;
}

$cacheKey = "status:{$cnpj}:{$ano}:{$sequencial}";
$ttl = $config['cache_ttl']['status'];

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/orgaos/{cnpj}/compras/{ano}/{sequencial}/historico
    $endpoint = "v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencial}/historico";
    $data = $client->request($endpoint);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
