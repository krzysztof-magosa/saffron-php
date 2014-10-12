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
use \KM\Saffron\MatchedRoute;

class MatchedRouteTest extends PHPUnit_Framework_TestCase
{
    public function testGetParam()
    {
        $route = new MatchedRoute(
            ['Controller', 'dispatch'],
            ['a' => 1, 'b' => 2, 'c' => 3]
        );

        $this->assertEquals(1, $route->getParam('a'));
        $this->assertEquals(2, $route->getParam('b'));
        $this->assertEquals(3, $route->getParam('c'));
    }

    public function testGetParams()
    {
        $route = new MatchedRoute(
            ['Controller', 'dispatch'],
            ['a' => 1, 'b' => 2, 'c' => 3]
        );

        $this->assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3],
            $route->getParams()
        );
    }

    public function testGetTarget()
    {
        $route = new MatchedRoute(
            ['Controller', 'dispatch'],
            ['a' => 1, 'b' => 2, 'c' => 3]
        );

        $this->assertEquals(
            ['Controller', 'dispatch'],
            $route->getTarget()
        );
    }
}
