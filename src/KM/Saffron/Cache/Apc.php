<?php
namespace KM\Saffron\Cache;

class Apc implements CacheInterface
{
    public function get($key)
    {
        return apc_fetch($key);
    }

    public function set($key, $value, $ttl)
    {
        return apc_store($key, $value, $ttl);
    }

    public function isSupported()
    {
        return extension_loaded('apc');
    }
}
