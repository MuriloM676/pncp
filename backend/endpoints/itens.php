<?php

$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';

if (!$cnpj || !$ano || !$sequencial) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj, ano and sequencial are required.']);
    exit;
}

$cacheKey = "itens:{$cnpj}:{$ano}:{$sequencial}";
$ttl = $config['cache_ttl']['itens'] ?? 300; // Default to 5 minutes if not set

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/orgaos/{cnpj}/compras/{ano}/{sequencial}/itens
    $endpoint = "v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencial}/itens";
    $data = $client->request($endpoint);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
