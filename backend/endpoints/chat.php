<?php

$idLicitacao = $_GET['idLicitacao'] ?? '';
if (!$idLicitacao) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'idLicitacao is required.']);
    exit;
}

$cacheKey = "chat:{$idLicitacao}";
$ttl = $config['cache_ttl']['chat'];

$data = $cache->get($cacheKey);

if (!$data) {
    $data = $client->request('obterChat', ['idLicitacao' => $idLicitacao]);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
