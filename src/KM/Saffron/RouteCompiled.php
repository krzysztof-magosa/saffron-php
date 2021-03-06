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

class RouteCompiled
{
    protected $prefix;
    protected $uriRegex;
    protected $domainRegex;

    /**
     * @param string $prefix
     * @param string $uriRegex
     * @param string $domainRegex
     */
    public function __construct($prefix, $uriRegex, $domainRegex)
    {
        $this->prefix = $prefix;
        $this->uriRegex = $uriRegex;
        $this->domainRegex = $domainRegex;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getUriRegex()
    {
        return $this->uriRegex;
    }

    /**
     * @return string
     */
    public function getDomainRegex()
    {
        return $this->domainRegex;
    }

    public function hasUriRegex()
    {
        return null !== $this->uriRegex;
    }
}
