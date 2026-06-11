<?php

$idLicitacao = $_GET['idLicitacao'] ?? '';
if (!$idLicitacao) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'idLicitacao is required.']);
    exit;
}

$cacheKey = "anexos:{$idLicitacao}";
$ttl = 3600; // Anexos mudam pouco

$data = $cache->get($cacheKey);

if (!$data) {
    $data = $client->request('obterAnexosLicitação', ['idLicitacao' => $idLicitacao]);
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
