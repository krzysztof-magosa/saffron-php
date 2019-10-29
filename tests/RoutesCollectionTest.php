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

use KM\Saffron\Exception\RouteAlreadyRegistered;
use KM\Saffron\RoutesCollection;
use PHPUnit\Framework\TestCase;

class RoutesCollectionTest extends TestCase
{
    public function testDuplicateOfNamedRoute()
    {
        $collection = new RoutesCollection();
        $collection->route('home')
            ->setUri('/');

        $this->expectException(RouteAlreadyRegistered::class);
        $this->expectExceptionMessage("Route with name home is already registered");
        $collection->route('home')
            ->setUri('/home');
    }

    public function testGroupByDomain()
    {
        $collection = new RoutesCollection();
        $routes[] = $collection->route('test1')->setDomain('www.example1.com');
        $routes[] = $collection->route('test2')->setDomain('www.example1.com');
        $routes[] = $collection->route('test3')->setDomain('www.example2.com');
        $routes[] = $collection->route('test4')->setDomain('www.example2.com');

        $grouped = $collection->groupByDomain();
        $keys = $grouped->getKeys();

        $this->assertEquals($routes[0], $grouped[$keys[0]][0]);
        $this->assertEquals($routes[1], $grouped[$keys[0]][1]);
        $this->assertEquals($routes[2], $grouped[$keys[1]][0]);
        $this->assertEquals($routes[3], $grouped[$keys[1]][1]);
    }

    public function testHasDomain()
    {
        $collection = new RoutesCollection();

        $this->assertEquals(false, $collection->hasDomain());
        $collection->route('test1')->setDomain('www.example1.com');
        $this->assertEquals(true, $collection->hasDomain());
    }

    public function testHasMethod()
    {
        $collection = new RoutesCollection();

        $this->assertEquals(false, $collection->hasMethod());
        $collection->route('test1')->setMethod('GET');
        $this->assertEquals(true, $collection->hasMethod());
    }

    public function testHasHttps()
    {
        $collection = new RoutesCollection();
        $route = $collection->route('test1');
        $this->assertEquals(false, $collection->hasHttps());

        $route->setHttps(true);
        $this->assertEquals(true, $collection->hasHttps());

        $route->setHttps(false);
        $this->assertEquals(true, $collection->hasHttps());
    }
}
