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
namespace KM\Saffron\UrlBuilder;

use KM\Saffron\Exception\NoSuchRoute;

abstract class Base
{
    protected $routes;

    /**
     * @param string $name
     */
    protected function hasRoute($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * @param string $name
     */
    protected function getRoute($name)
    {
        if (!$this->hasRoute($name)) {
            throw new NoSuchRoute("There is no route $name.");
        }

        return $this->routes[$name];
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param boolean $fullUrl
     */
    public function assemble($name, array $parameters = [], $fullUrl = false)
    {
        $route = $this->getRoute($name);

        $values = array_replace(
            $route['defaults'],
            $parameters
        );

        $uri = $route['uri'];
        foreach ($values as $name => $value) {
            $uri = str_replace('{'.$name.'}', $value, $uri);
        }

        if ($fullUrl) {
            if (null !== $route['https']) {
                $scheme = $route['https'] ? 'https://' : 'http://';
            } else {
                $scheme = 'http://';
            }

            $domain = $route['domain'];
            foreach ($values as $name => $value) {
                $domain = str_replace('{'.$name.'}', $value, $domain);
            }

            return $scheme.$domain.$uri;
        } else {
            return $uri;
        }
    }
}
