<?php
namespace KM\Saffron\Cache;

class None implements CacheInterface
{
    public function get($key)
    {
        return false;
    }

    public function set($key, $value, $ttl)
    {
        return false;
    }
}
