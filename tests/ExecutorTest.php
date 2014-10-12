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
use \KM\Saffron\Executor;

class Controller
{
    public function dispatch($a, $b, $c)
    {
        return [$a, $b, $c];
    }
}

class ExecutorTest extends PHPUnit_Framework_TestCase
{
    public function testByName()
    {
        $executor = new \KM\Saffron\Executor();
        $vars = $executor
            ->setController('Controller')
            ->setMethod('dispatch')
            ->setParameters(
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ]
            )
            ->fire();

        $this->assertEquals(1, $vars[0]);
        $this->assertEquals(2, $vars[1]);
        $this->assertEquals(3, $vars[2]);
    }

    public function testByObject()
    {
        $executor = new \KM\Saffron\Executor();
        $vars = $executor
            ->setController(new Controller())
            ->setMethod('dispatch')
            ->setParameters(
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ]
            )
            ->fire();

        $this->assertEquals(1, $vars[0]);
        $this->assertEquals(2, $vars[1]);
        $this->assertEquals(3, $vars[2]);
    }

    public function testMatchedRoute()
    {
        $route = new MatchedRoute(
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $executor = new Executor($route);
        $vars = $executor->fire();

        $this->assertEquals('11', $vars[0]);
        $this->assertEquals('12', $vars[1]);
        $this->assertEquals('13', $vars[2]);
    }
}
