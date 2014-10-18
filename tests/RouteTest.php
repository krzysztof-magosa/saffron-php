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
use \KM\Saffron\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $route1 = new Route('name1');
        $this->assertEquals('name1', $route1->getName());
    }

    public function testRequirements()
    {
        $route = new Route('name1');
        $route->setRequirements(['id' => '\d+']);
        $this->assertEquals(['id' => '\d+'], $route->getRequirements());
    }
}
