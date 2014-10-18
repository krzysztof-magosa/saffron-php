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

use KM\Saffron\Exception\RouteAlreadyRegistered;

class RoutesCollection extends \ArrayIterator
{
    /**
     * @param string $name Name of route
     * @return RoutesCollection
     */
    public function route($name)
    {
        if ($this->offsetExists($name)) {
            throw new RouteAlreadyRegistered("Route with name $name is already registered");
        }

        $route = new Route($name);
        $this[$name] = $route;
        return $route;
    }

    public function first()
    {
        return $this[$this->getFirstKey()];
    }

    protected function getFirstKey()
    {
        foreach ($this as $key => $value) {
            return $key;
        }

        return null;
    }

    protected function groupBy(\Closure $func)
    {
        $index = 0;
        $lastValue = $func($this->first());

        $result = new self();

        foreach ($this as $route) {
            if ($lastValue != ($value = $func($route))) {
                $lastValue = $value;
                $index++;
            }

            if (!isset($result[$index])) {
                $result[$index] = new self();
            }

            $result[$index]->append($route);
        }

        return $result;        
    }

    public function groupByDomain()
    {
        return $this->groupBy(
            function ($route) {
                return $route->getDomain();
            }
        );
    }

    protected function has(\Closure $func)
    {
        foreach ($this as $route) {
            if ($func($route)) {
                return true;
            }
        }

        return false;
    }

    public function hasDomain()
    {
        return $this->has(
            function ($route) {
                return $route->hasDomain();
            }
        );
    }

    public function hasMethod()
    {
        return $this->has(
            function ($route) {
                return $route->hasMethod();
            }
        );
    }

    public function hasHttps()
    {
        return $this->has(
            function ($route) {
                return $route->hasHttps();
            }
        );
    }
}
