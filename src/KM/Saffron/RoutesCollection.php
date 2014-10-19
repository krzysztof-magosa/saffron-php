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
use KM\Saffron\Exception\EmptyCollection;
use KM\Saffron\Collection;

class RoutesCollection extends Collection
{
    /**
     * Create instance of Route and returns it.
     * Also looks for duplicated names.
     *
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

    /**
     * Groups routes by domain
     * @mixed RoutesCollection
     */
    public function groupByDomain()
    {
        return $this->groupBy(
            function ($route) {
                return sha1($route->getDomain());
            }
        );
    }

    /**
     * Checks whether routes in collection has domain condition.
     * @return bool
     */
    public function hasDomain()
    {
        return $this->has(
            function ($route) {
                return $route->hasDomain();
            }
        );
    }

    /**
     * Checks whether routes in collection has method condition.
     * @return bool
     */
    public function hasMethod()
    {
        return $this->has(
            function ($route) {
                return $route->hasMethod();
            }
        );
    }

    /**
     * Checks whether routes in collection has https condition.
     * @return bool
     */
    public function hasHttps()
    {
        return $this->has(
            function ($route) {
                return $route->hasHttps();
            }
        );
    }
}
