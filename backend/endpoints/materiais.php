<?php

$descricao = $_GET['descricao'] ?? '';
$pagina = $_GET['pagina'] ?? 1;

if (!$descricao) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'descricao is required.']);
    exit;
}

$cacheKey = "material:" . md5($descricao) . ":p{$pagina}";
$ttl = 3600; // Catálogo muda pouco

$data = $cache->get($cacheKey);

if (!$data) {
    $params = [
        'pdm_descricao' => $descricao,
        'pagina' => $pagina
    ];
    
    $data = $comprasClient->request('modulo-material/3_consultarPdmMaterial', $params);
    
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
