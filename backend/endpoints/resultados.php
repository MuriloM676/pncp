<?php

$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';
$numeroItem = $_GET['numero_item'] ?? '';

if (!$cnpj || !$ano || !$sequencial || !$numeroItem) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'cnpj, ano, sequencial and numero_item are required.']);
    exit;
}

$cacheKey = "resultados:{$cnpj}:{$ano}:{$sequencial}:{$numeroItem}";
$ttl = $config['cache_ttl']['processo'] ?? 600; // Usa TTL de processo (10 min) como padrão

$data = $cache->get($cacheKey);

if (!$data) {
    // Rota PNCP: /v1/orgaos/{cnpj}/compras/{ano}/{sequencial}/itens/{numeroItem}/resultados
    $endpoint = "v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencial}/itens/{$numeroItem}/resultados";
    $data = $client->request($endpoint);
    
    if (is_array($data)) {
        // Normalização dos campos principais
        $normalized = array_map(function($item) {
            return [
                'cnpjVencedor' => $item['niFornecedor'] ?? '',
                'razaoSocial' => $item['nomeRazaoSocialFornecedor'] ?? '',
                'valorUnitario' => $item['valorUnitarioHomologado'] ?? 0,
                'valorTotal' => $item['valorTotalHomologado'] ?? 0,
                'quantidade' => $item['quantidadeHomologada'] ?? 0,
                'situacao' => $item['situacaoCompraItemResultadoNome'] ?? 'Homologado',
                'porteFornecedor' => $item['porteFornecedorNome'] ?? ''
            ];
        }, $data);
        $data = $normalized;
    }
    
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
