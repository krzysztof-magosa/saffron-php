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

class Route
{
    protected $name;
    protected $uri;
    protected $domain;
    protected $https;
    protected $method = [];
    protected $requires = [];
    protected $defaults = [];
    protected $target = [];

    public function __construct($name)
    {
        $this->setName($name);
    }

    protected function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function hasDomain()
    {
        return !empty($this->domain);
    }

    public function setMethod($method)
    {
        $this->method = (array)$method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function hasMethod()
    {
        return !empty($this->method);
    }

    public function setHttps($https)
    {
        $this->https = $https;
        return $this;
    }

    public function getHttps()
    {
        return $this->https;
    }

    public function setRequires(array $requires)
    {
        $this->requires = $requires;
        return $this;
    }

    public function getRequires()
    {
        return $this->requires;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function hasDefault($name)
    {
        return isset($this->defaults[$name]);
    }

    public function setTarget($controller, $method = 'indexAction')
    {
        $this->target = [$controller, $method];
        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getPlaceholders()
    {
        preg_match_all('@{(\w+)}@Us', $this->uri, $match);
        return $match[1];
    }

    public function getPrefix()
    {
        // @TODO make it not ugly :)
        $pos = strpos($this->uri, '{');

        if (false !== $pos) {
            $length = max($pos - 1, 1);
        }
        else {
            $length = strlen($this->uri);
        }

        return substr($this->uri, 0, $length);
    }

    public function getUriRegex()
    {
        $regex = preg_quote($this->uri, '#');

        foreach ($this->getPlaceholders() as $placeholder) {
            if (isset($this->requires[$placeholder])) {
                $require = $this->requires[$placeholder];
            } else {
                $require = '.+';
            }

            // magic is here, don't touch
            $regex = preg_replace(
                '#(.)(.)?\\\\{('.$placeholder.')\\\\}#Us',
                '\1(\2(?P<\3>'.$require.'))' . ($this->hasDefault($placeholder) ? '?' : ''),
                $regex
            );
        }

        return '#^'.$regex.'$#Us';
    }

    public function getDomainRegex()
    {
        $regex = preg_quote($this->domain, '#');
    
        // @TODO

        return '#^'.$regex.'$#Us';
    }    

    public function needsUriRegex()
    {
        return $this->getPrefix() != $this->getUri();
    }
}
