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
    protected $classSuffix;
    protected $debug = false;

    public function __construct(callable $initClosure)
    {
        $this->initClosure = $initClosure;
    }

    /**
     * @param string $dir
     * @return RouterFactory
     */
    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        if (!$this->cacheDir) {
            $this->cacheDir = sys_get_temp_dir();
        }

        return $this->cacheDir;
    }

    /**
     * @param string $suffix
     * @return RouterFactory
     */
    public function setClassSuffix($suffix)
    {
        $this->classSuffix = $suffix;
        return $this;
    }

    /**
     * @param bool $debug
     * @return RouterFactory
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassSuffix()
    {
        static $iteration = 0;

        if ($this->debug) {
            return $this->classSuffix.md5(microtime(true).(++$iteration));
        }

        return $this->classSuffix;
    }

    /**
     * @param string $className
     * @param \Closure $generator
     * @return object
     */
    protected function createInstance($className, \Closure $generator)
    {
        $cacheFile = $this->getCacheDir().'/'.$className.'.php';

        if (!is_readable($cacheFile) || $this->debug) {
            file_put_contents(
                $cacheFile,
                $generator($className),
                LOCK_EX
            );
        }

        require_once $cacheFile;

        return new $className;
    }

    /**
     * @return UrlMatcher\Base
     */
    public function getUrlMatcher()
    {
        return $this->createInstance(
            'KM_Saffron_UrlMatcher_'.$this->getClassSuffix(),
            function ($className) {
                $generator = new UrlMatcher\Generator($this->getCollection());
                return $generator->generate($className);
            }
        );
    }

    /**
     * @return UrlBuilder\Base
     */
    public function getUrlBuilder()
    {
        return $this->createInstance(
            'KM_Saffron_UrlBuilder_'.$this->getClassSuffix(),
            function ($className) {
                $generator = new UrlBuilder\Generator($this->getCollection());
                return $generator->generate($className);
            }
        );
    }

    /**
     * @return RoutesCollection
     */
    protected function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = new RoutesCollection();
            call_user_func($this->initClosure, $this->collection);
        }

        return $this->collection;
    }

    /**
     * @return Router
     */
    public function build()
    {
        return new Router($this);
    }
}
