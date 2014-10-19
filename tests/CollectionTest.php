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
use KM\Saffron\Collection;

class MyCollection extends Collection
{
}

class Entity
{
    public $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
}

class CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \KM\Saffron\Exception\EmptyCollection
     * @expectedExceptionMessage You cannot fetch first element of empty collection.
     */
    public function testFirstOnEmptyCollection()
    {
        $collection = new MyCollection();
        $collection->first();
    }

    public function testFirstOnFullCollection()
    {
        $collection = new MyCollection();
        $route1 = $collection[0] = new Entity('test1');
        $collection[1] = new Entity('test2');

        $this->assertEquals($route1, $collection->first());
    }

    public function testGetKeys()
    {
        $collection = new MyCollection();
        $collection['key1'] = new \stdClass;
        $collection['key51'] = new \stdClass;
        $collection['key91'] = new \stdClass;

        $this->assertEquals(['key1', 'key51', 'key91'], $collection->getKeys());
    }
}
