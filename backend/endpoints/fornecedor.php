<?php

$cnpj = $_GET['cnpj'] ?? '';
if (!$cnpj) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj is required.']);
    exit;
}

$cacheKey = "fornecedor:{$cnpj}";
$ttl = $config['cache_ttl']['fornecedor'];

$data = $cache->get($cacheKey);

if (!$data) {
    $data = $client->request('processosFornecedor', ['documentoFornecedor' => $cnpj]);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
