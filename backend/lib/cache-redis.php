<?php

class RedisCache {
    private $redis;
    private static $instance = null;

    private function __construct($config) {
        try {
            $this->redis = new Redis();
            $this->redis->connect($config['host'], $config['port']);
        } catch (Exception $e) {
            Logger::error("Falha ao conectar ao Redis: " . $e->getMessage());
            $this->redis = null;
        }
    }

    public static function getInstance($config) {
        if (self::$instance === null) {
            self::$instance = new RedisCache($config);
        }
        return self::$instance;
    }

    public function get($key) {
        if (!$this->redis) return null;
        $value = $this->redis->get($key);
        return $value ? json_decode($value, true) : null;
    }

    public function set($key, $value, $ttl = 3600) {
        if (!$this->redis) return false;
        return $this->redis->setex($key, $ttl, json_encode($value));
    }

    public function delete($key) {
        if (!$this->redis) return false;
        return $this->redis->del($key);
    }

    public function increment($key, $ttl = 60) {
        if (!$this->redis) return 1;
        $count = $this->redis->incr($key);
        if ($count === 1) {
            $this->redis->expire($key, $ttl);
        }
        return $count;
    }
}
