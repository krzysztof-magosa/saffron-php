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
use KM\Saffron\RoutesCollection;

class RoutesCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \KM\Saffron\Exception\RouteAlreadyRegistered
     * @expectedExceptionMessage Route with name home is already registered
     */
    public function testDuplicateOfNamedRoute()
    {
        $collection = new RoutesCollection();
        $collection->route('home')
            ->setUri('/');

        $collection->route('home')
            ->setUri('/home');
    }

    /**
     * @expectedException \KM\Saffron\Exception\EmptyCollection
     * @expectedExceptionMessage You cannot fetch first element of empty collection.
     */
    public function testFirstOnEmptyCollection()
    {
        $collection = new RoutesCollection();
        $collection->first();
    }

    public function testFirstOnFullCollection()
    {
        $collection = new RoutesCollection();
        $route1 = $collection->route('home');
        $route2 = $collection->route('team');

        $this->assertEquals($route1, $collection->first());
    }
}
