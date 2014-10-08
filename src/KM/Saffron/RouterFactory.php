<?php
/**
 * Copyright 2014 Krzysztof Magosa
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace KM\Saffron;

class RouterFactory
{
    protected $cacheKey;
    protected $cacheAdapter;
    protected $cacheTtl = 10;

    public function setCacheTtl($ttl)
    {
        $this->cacheTtl = $ttl;
        return $this;
    }

    public function getCacheTtl()
    {
        return $this->cacheTtl;
    }

    public function setCacheKey($key)
    {
        $this->cacheKey = $key;
        return $this;
    }

    public function getCacheKey()
    {
        if (!$this->cacheKey) {
            // When you use composer, each application has own copy of this file.
            // Therefore cache key based on directory to this file should be unique.
            $this->cacheKey = sha1(__DIR__);
        }

        return $this->cacheKey;
    }

    public function setCacheAdapter(Cache\CacheInterface $adapter)
    {
        $this->cacheAdapter = $adapter;
        return $this;
    }

    protected function getCacheAdapter()
    {
        if (!$this->cacheAdapter) {
            $this->setCacheAdapter(
                new Cache\FirstSupported(
                    [
                        new Cache\Apc(),
                        new Cache\None()
                    ]
                )
            );
        }

        return $this->cacheAdapter;
    }

    /**
     * Returns builded object, if possible from cache.
     * 
     * @return \KM\Saffron\Router
     */
    public function build(\Closure $init)
    {
        $cacheAdapter = $this->getCacheAdapter();
        $router = $cacheAdapter->get($this->getCacheKey());

        if (!$router) {
            $router = new Router();
            $init($router);

            $cacheAdapter->set(
                $this->getCacheKey(),
                $router,
                $this->getCacheTtl()
            );
        }

        return $router;
    }
}
