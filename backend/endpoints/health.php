<?php

echo json_encode([
    'status' => 'ok',
    'timestamp' => time(),
    'redis' => RedisCache::getInstance($config['redis']) ? 'connected' : 'disconnected'
]);
