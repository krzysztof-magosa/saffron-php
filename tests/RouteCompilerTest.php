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
use KM\Saffron\Route;

class RouteCompilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \KM\Saffron\Exception\InvalidArgument
     * @expectedExceptionMessage Placeholders cannot begin with _. Route: route.
     */
    public function testInvalidPlaceholderInUri()
    {
        $route = new Route('route');
        $route->setUri('/{_name}');
        $route->getCompiled();
    }


    /**
     * @expectedException \KM\Saffron\Exception\InvalidArgument
     * @expectedExceptionMessage Placeholders cannot begin with _. Route: route.
     */
    public function testInvalidPlaceholderInDomain()
    {
        $route = new Route('route');
        $route->setDomain('{_name}.example.com');
        $route->getCompiled();
    }
}
