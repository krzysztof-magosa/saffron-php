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
    protected $requirements = [];
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

    public function hasHttps()
    {
        return null !== $this->https;
    }

    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
        return $this;
    }

    public function getRequire($name)
    {
        return isset($this->requirements[$name]) ? $this->requirements[$name] : '.+';
    }

    public function getRequirements()
    {
        return $this->requirements;
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

    public function hasDefaults()
    {
        return !empty($this->defaults);
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

    public function getUriPlaceholders()
    {
        preg_match_all('@{(\w+)}@Us', $this->uri, $match);
        return $match[1];
    }

    public function getDomainPlaceholders()
    {
        preg_match_all('@{(\w+)}@Us', $this->domain, $match);
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

    public function isOptionalUriParameter($name)
    {
        foreach (array_reverse($this->getUriPlaceholders()) as $item) {
            if (!$this->hasDefault($item)) {
                break;
            }

            if ($item == $name) {
                return true;
            }
        }

        return false;
    }

    public function isOptionalDomainParameter($name)
    {
        foreach ($this->getDomainPlaceholders() as $item) {
            if (!$this->hasDefault($item)) {
                break;
            }

            if ($item == $name) {
                return true;
            }
        }

        return false;
    }

    public function getUriRegex()
    {
        $tokens = preg_split(
            '#([^}]?\{\w+\})#s',
            substr($this->uri, 1),
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        
        $regex = preg_quote(substr($this->uri, 0, 1), '#');
        foreach ($tokens as $token) {
            if (preg_match('#^(?P<delimiter>.)?\{(?P<placeholder>\w+)\}$#s', $token, $match)) {
                $regex .= sprintf(
                    '(%s(?P<%s>%s))%s',
                    isset($match['delimiter']) ? preg_quote($match['delimiter'], '#') : '',
                    preg_quote($match['placeholder'], '#'),
                    $this->getRequire($match['placeholder']),
                    $this->isOptionalUriParameter($match['placeholder']) ? '?' : ''
                );
            }
            else {
                $regex .= preg_quote($token, '#');
            }
        }

        return '#^'.$regex.'$#s';
    }

    public function getDomainRegex()
    {
        $tokens = preg_split(
            '#({\w+\}[^{]?)#s',
            $this->domain,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $regex = '';
        foreach ($tokens as $token) {
            if (preg_match('#^\{(?P<placeholder>\w+)\}(?P<delimiter>.)?$#s', $token, $match)) {
                $regex .= sprintf(
                    '((?P<%s>%s)%s)%s',
                    preg_quote($match['placeholder'], '#'),
                    $this->getRequire($match['placeholder']),
                    isset($match['delimiter']) ? preg_quote($match['delimiter'], '#') : '',
                    $this->isOptionalDomainParameter($match['placeholder']) ? '?' : ''
                );
            }
            else {
                $regex .= preg_quote($token, '#');
            }
        }

        return '#^'.$regex.'$#s';
    }    

    public function needsUriRegex()
    {
        return $this->getPrefix() != $this->getUri();
    }
}
