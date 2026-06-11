<?php

$tipo = $_GET['tipo'] ?? 'grupos';
$params = [];
if (isset($_GET['pagina'])) $params['pagina'] = $_GET['pagina'];

$cacheKey = "materiais:{$tipo}:" . http_build_query($params);
$ttl = 3600;

$data = $cache->get($cacheKey);

if (!$data) {
    if ($tipo === 'grupos') {
        $data = $comprasClient->getGruposMaterial($params);
    } elseif ($tipo === 'classes') {
        $data = $comprasClient->getClassesMaterial($params);
    }
    
    if ($data) {
        $cache->set($cacheKey, $data, $ttl);
    }
}

echo json_encode($data ?: []);
