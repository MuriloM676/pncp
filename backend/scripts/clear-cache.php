<?php

$config = require_once __DIR__ . '/../lib/config.php';

try {
    $redis = new Redis();
    $redis->connect($config['redis']['host'], $config['redis']['port']);
    $redis->flushAll();
    echo "Cache Redis limpo com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao limpar cache: " . $e->getMessage() . "\n";
}
