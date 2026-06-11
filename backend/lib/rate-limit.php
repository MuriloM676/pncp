<?php

class RateLimiter {
    private $cache;
    private $limit;
    private $window;

    public function __construct($cache, $limit = 60, $window = 60) {
        $this->cache = $cache;
        $this->limit = $limit;
        $this->window = $window;
    }

    public function check($ip) {
        $key = "rate_limit:" . $ip;
        $current = $this->cache->increment($key, $this->window);
        return $current <= $this->limit;
    }
}
