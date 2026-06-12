<?php

$cacheKey = "modalidades:list";
$ttl = 86400; // Cache por 24 horas

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/modalidades
    $endpoint = "v1/modalidades";
    $data = $client->request($endpoint);
    
    if (is_array($data)) {
        // Filtrar apenas ativas e ordenar por ID
        $data = array_filter($data, function($item) {
            return !empty($item['statusAtivo']);
        });
        
        // Normalizar
        $normalized = array_map(function($item) {
            return [
                'id' => $item['id'],
                'nome' => $item['nome'],
                'descricao' => $item['descricao'] ?? ''
            ];
        }, $data);
        
        // Reindexar array e ordenar por ID
        usort($normalized, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });
        
        $data = $normalized;
    }
    
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
