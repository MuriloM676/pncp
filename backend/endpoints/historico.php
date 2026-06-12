<?php

$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';

if (!$cnpj || !$ano || !$sequencial) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj, ano and sequencial are required.']);
    exit;
}

$cacheKey = "historico:{$cnpj}:{$ano}:{$sequencial}";
$ttl = $config['cache_ttl']['status'] ?? 600; // Default to 10 minutes

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/orgaos/{cnpj}/compras/{ano}/{sequencial}/historico
    $endpoint = "v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencial}/historico";
    $data = $client->request($endpoint);
    
    if (is_array($data)) {
        // Normalization
        $normalized = array_map(function($item) {
            return [
                'historicoStatusNome' => $item['categoriaLogManutencao'] ?? 'Evento',
                'data' => $item['logManutencaoDataInclusao'] ?? '',
                'descricao' => $item['justificativa'] ?? ''
            ];
        }, $data);
        
        // Sort by date (assuming chronological)
        usort($normalized, function($a, $b) {
            return strcmp($b['data'], $a['data']); // Descending
        });
        
        $data = $normalized;
    }

    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
