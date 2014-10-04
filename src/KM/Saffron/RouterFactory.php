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
    protected $cacheTtl = 600;
    protected $routes = [];

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

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * If possible tries to use APC to store result of $closure.
     * If APC is not installed, just returns result of $closure.
     * 
     * @param \Closure $closure Closure to be cached.
     * @return mixed The result of closure.
     */
    protected function cache(\Closure $closure)
    {
        if (extension_loaded('apc')) {
            $key = $this->getCacheKey();
            $data = apc_fetch($key, $success);

            if (!$success) {
                $data = $closure();
                apc_store($key, $data, $this->getCacheTtl());
            }
        }
        else {
            $data = $closure();
        }

        return $data;
    }

    /**
     * Returns builded object, if possible from cache.
     * 
     * @return \KM\Saffron\Router
     */
    public function build()
    {
        return $this->cache(
            function () {
                $router = new \KM\Saffron\Router();
                foreach ($this->routes as $route) {
                    $router->append($route);
                }

                return $router;
            }
        );
    }
}
