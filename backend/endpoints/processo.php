<?php

$id = $_GET['id'] ?? '';

if (!$id) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'id is required.']);
    exit;
}

$cacheKey = "processo:{$id}";
$ttl = $config['cache_ttl']['processo'];

$data = $cache->get($cacheKey);

if (!$data) {
    // Endpoint para consultar detalhes de uma contratação específica por ID
    // Rota: /modulo-contratacoes/1.1_consultarContratacoes_PNCP_14133_Id
    $endpoint = "modulo-contratacoes/1.1_consultarContratacoes_PNCP_14133_Id";
    $data = $client->request($endpoint, ['id' => $id]);
    
    if ($data && isset($data['resultado'])) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

if ($data && isset($data['resultado'])) {
    echo json_encode($data['resultado']);
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Process not found on Compras.gov.br API.']);
}
