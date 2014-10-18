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
    protected $collection;
    protected $initClosure;
    protected $cacheDir;
    protected $cacheSuffix;
    protected $debug = false;

    public function __construct(callable $initClosure)
    {
        $this->initClosure = $initClosure;
    }

    public function setCacheDir($dir)
    {
        $this->cacheDir = rtrim(realpath($dir), '/');
        return $this;
    }

    public function getCacheDir()
    {
        if (!$this->cacheDir) {
            $this->cacheDir = sys_get_temp_dir();
        }

        return $this->cacheDir;
    }

    public function setCacheSuffix($suffix)
    {
        $this->cacheSuffix = $suffix;
        return $this;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    public function getCacheSuffix()
    {
        static $iteration = 0;

        if ($this->debug) {
            return $this->cacheSuffix.md5(microtime(true).(++$iteration));
        }

        return $this->cacheSuffix;
    }

    public function getUrlMatcher()
    {
        $className = 'KM_Saffron_UrlMatcher_'.$this->getCacheSuffix();
        $cacheFile = $this->getCacheDir().'/'.$className.'.php';

        if (!is_readable($cacheFile) || $this->debug) {
            $generator = new UrlMatcher\Generator($this->getCollection());

            file_put_contents(
                $cacheFile,
                $generator->generate($className),
                LOCK_EX
            );
        }

        require_once $cacheFile;

        return new $className();
    }

    public function getUrlBuilder()
    {
        $className = 'KM_Saffron_UrlBuilder_'.$this->getCacheSuffix();
        $cacheFile = $this->getCacheDir().'/'.$className.'.php';

        if (!is_readable($cacheFile) || $this->debug) {
            $generator = new UrlBuilder\Generator($this->getCollection());

            file_put_contents(
                $cacheFile,
                $generator->generate($className),
                LOCK_EX
            );
        }

        require_once $cacheFile;

        return new $className();
    }

    protected function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = new RoutesCollection();
            call_user_func($this->initClosure, $this->collection);
        }

        return $this->collection;
    }

    public function build()
    {
        return new Router($this);
    }
}
