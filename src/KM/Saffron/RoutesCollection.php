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
    protected $routes = [];

    public function route($name)
    {
        if ($this->offsetExists($name)) {
            throw new RouteAlreadyRegistered("Route with name $name is already registered");
        }

        $route = new Route($name);
        $this[$name] = $route;
        return $route;
    }
}
