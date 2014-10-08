<?php
namespace KM\Saffron\Cache;

interface CacheInterface
{
    public function get($key);
    public function set($key, $value, $ttl);
    public function isSupported();
}
