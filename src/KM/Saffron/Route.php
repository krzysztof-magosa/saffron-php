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

use KM\Saffron\RouteCompiler;
use KM\Saffron\RouteCompiled;

class Route
{
    protected $name;
    protected $uri;
    protected $domain;
    protected $https;
    protected $method = [];
    protected $requirements = [];
    protected $defaults = [];
    protected $target = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $uri
     * @return Route
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $domain
     * @return Route
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function hasDomain()
    {
        return !empty($this->domain);
    }

    /**
     * @param array|string $method
     * @return Route
     */
    public function setMethod($method)
    {
        $this->method = (array)$method;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function hasMethod()
    {
        return !empty($this->method);
    }

    /**
     * @param mixed $https True - only https is allowed, false - only non-https is allowed, null - doesn't matter
     * @return Route
     */
    public function setHttps($https)
    {
        $this->https = $https;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * @return bool
     */
    public function hasHttps()
    {
        return null !== $this->https;
    }

    /**
     * @param array $requirements
     * @return Route
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getRequirement($name)
    {
        return isset($this->requirements[$name]) ? $this->requirements[$name] : '.+';
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param array $defaults
     * @return Route
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasDefault($name)
    {
        return isset($this->defaults[$name]);
    }

    /**
     * @return bool
     */
    public function hasDefaults()
    {
        return !empty($this->defaults);
    }

    /**
     * @param string $controller
     * @param string $method
     * @return Route
     */
    public function setTarget($controller, $method = 'indexAction')
    {
        $this->target = [$controller, $method];
        return $this;
    }

    /**
     * @return array
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return RouteCompiler
     */
    protected function getCompiler()
    {
        static $compiler;

        if (!$compiler) {
            $compiler = new RouteCompiler();
        }

        return $compiler;
    }

    /**
     * @return RouteCompiled
     */
    public function getCompiled()
    {
        return $this->getCompiler()->compile($this);
    }
}
