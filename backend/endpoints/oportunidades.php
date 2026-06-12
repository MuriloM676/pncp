<?php

$uf = $_GET['uf'] ?? '';
$pagina = $_GET['pagina'] ?? 1;
$cacheKey = "oportunidades:" . ($uf ?: 'todos') . ":p{$pagina}";
$ttl = $config['cache_ttl']['oportunidades'];

$data = $cache->get($cacheKey);

if (!$data) {
    // Usando o endpoint do módulo de contratações (Lei 14.133)
    // Rota: /modulo-contratacoes/1_consultarContratacoes_PNCP_14133
    $params = [
        'pagina' => $pagina,
        'tamanhoPagina' => 10
    ];
    if ($uf) $params['uf'] = $uf;
    
    $data = $comprasClient->getContratacoes($params);
    
    if ($data && isset($data['resultado'])) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

if ($data && isset($data['resultado'])) {
    echo json_encode($data);
} else {
    header('HTTP/1.1 502 Bad Gateway');
    $msg = ($data && isset($data['message'])) ? $data['message'] : 'Could not fetch data from Compras.gov.br API.';
    echo json_encode(['error' => $msg]);
}
