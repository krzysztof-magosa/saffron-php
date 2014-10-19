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

class Request
{
    protected $domain;
    protected $uri;
    protected $method;
    protected $https;

    /**
     * Returns domain or null when not set
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets domain
     *
     * @return \KM\Saffron\Request
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Returns uri or null when not set
     *
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets uri
     * @return \KM\Saffron\Request
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Returns method or null when not set
     *
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets method
     *
     * @return \KM\Saffron\Request
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Returns info whether connection is secured by https
     *
     * @return bool
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * Sets info whether connection is secured by https
     *
     * @return \KM\Saffron\Request
     */
    public function setHttps($https)
    {
        $this->https = $https;
        return $this;
    }

    /**
     * Builds request object from super globals
     *
     * @return \KM\Saffron\Request
     */
    static public function createFromGlobals()
    {
        $instance = new static();
        $instance
            ->setUri(explode('?', $_SERVER['REQUEST_URI'])[0])
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setDomain($_SERVER['HTTP_HOST'])
            ->setHttps(
                !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'
            );

        return $instance;
    }
}
