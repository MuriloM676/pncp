<?php

// Parâmetros necessários para a API do PNCP: cnpj, ano, sequencial
$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';

if (!$cnpj || !$ano || !$sequencial) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj, ano and sequencial are required.']);
    exit;
}

$cacheKey = "anexos:{$cnpj}:{$ano}:{$sequencial}";
$ttl = 3600;

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/orgaos/{cnpj}/compras/{ano}/{sequencial}/arquivos
    $endpoint = "v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencial}/arquivos";
    $data = $client->request($endpoint);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
