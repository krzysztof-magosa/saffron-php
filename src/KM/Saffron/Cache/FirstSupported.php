<?php
namespace KM\Saffron\Cache;

class FirstSupported implements CacheInterface
{
    protected $adapter;

    public function __construct(array $adapters = [])
    {
        foreach ($adapters as $adapter) {
            if ($adapter->isSupported()) {
                $this->adapter = $adapter;
            }
        }

        if (!$this->adapter) {
            throw new \LogicException('You must provide at least supported adapter.');
        }
    }

    public function get($key)
    {
        return $this->adapter->get($key);
    }

    public function set($key, $value, $ttl)
    {
        return $this->adapter->set($key, $value, $ttl);
    }

    public function isSupported()
    {
        return true;
    }
}
