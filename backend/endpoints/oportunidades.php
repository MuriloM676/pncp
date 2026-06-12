<?php

$uf = $_GET['uf'] ?? '';
$cidadeNome = $_GET['cidade_nome'] ?? '';
$cidadeIbge = $_GET['cidade_ibge'] ?? ''; // Existing IBGE support
$pagina = $_GET['pagina'] ?? 1;
$busca = $_GET['busca'] ?? '';
$modalidade = $_GET['modalidade'] ?? '';
$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal = $_GET['data_final'] ?? '';

// Mapeamento de nome para IBGE, se cidade_nome for fornecido
if ($cidadeNome && empty($cidadeIbge)) {
    $cidadesFile = __DIR__ . '/../data/cidades.json';
    if (file_exists($cidadesFile)) {
        $cidadesMap = json_decode(file_get_contents($cidadesFile), true);
        $key = mb_strtolower($cidadeNome);
        if (isset($cidadesMap[$key])) {
            $cidadeIbge = $cidadesMap[$key];
        }
    }
}

// Cache key contendo todos os filtros aplicados
$cacheKeyParts = [
    "oportunidades",
    $uf ?: 'todos',
    $cidadeIbge ?: 'todos',
    $busca ? md5($busca) : 'todos',
    $modalidade ?: 'todos',
    $dataInicial ?: 'todos',
    $dataFinal ?: 'todos',
    "p{$pagina}"
];
$cacheKey = implode(':', $cacheKeyParts);
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
    if ($cidadeIbge) $params['unidadeCodigoIbge'] = $cidadeIbge;
    if ($busca) $params['objeto'] = $busca;
    if ($modalidade) $params['codigoModalidade'] = $modalidade;
    if ($dataInicial) $params['dataPublicacaoPncpInicial'] = $dataInicial;
    if ($dataFinal) $params['dataPublicacaoPncpFinal'] = $dataFinal;
    
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

