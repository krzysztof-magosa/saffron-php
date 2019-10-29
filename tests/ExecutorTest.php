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
use \KM\Saffron\RoutingResult;
use \KM\Saffron\Executor;
use PHPUnit\Framework\TestCase;

class Controller
{
    public function dispatch($a, $b, $c)
    {
        global $steps;
        $steps[] = 'dispatch';
        return [$a, $b, $c];
    }
}

class ExecutorTest extends TestCase
{
    public function testByName()
    {
        $executor = new Executor();
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
        $executor = new Executor();
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

    public function testRoutingResult()
    {
        $result = new RoutingResult(
            true,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $executor = new Executor($result);
        $vars = $executor->fire();

        $this->assertEquals('11', $vars[0]);
        $this->assertEquals('12', $vars[1]);
        $this->assertEquals('13', $vars[2]);
    }

    public function testPreDispatch()
    {
        global $steps;

        $result = new RoutingResult(
            true,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $steps = [];
        $executor = new Executor($result);
        $executor->setPreDispatch(
            function () {
                global $steps;
                $steps[] = 'preDispatch';
            }
        );
        $executor->fire();

        $this->assertEquals(['preDispatch', 'dispatch'], $steps);
    }

    public function testPostDispatch()
    {
        global $steps;

        $result = new RoutingResult(
            true,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $steps = [];
        $executor = new Executor($result);
        $executor->setPostDispatch(
            function () {
                global $steps;
                $steps[] = 'postDispatch';
            }
        );
        $executor->fire();

        $this->assertEquals(['dispatch', 'postDispatch'], $steps);
    }

    public function testPreWithPostDispatch()
    {
        global $steps;

        $result = new RoutingResult(
            true,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $steps = [];
        $executor = new Executor($result);
        $executor->setPreDispatch(
            function () {
                global $steps;
                $steps[] = 'preDispatch';
            }
        );
        $executor->setPostDispatch(
            function () {
                global $steps;
                $steps[] = 'postDispatch';
            }
        );
        $executor->fire();

        $this->assertEquals(['preDispatch', 'dispatch', 'postDispatch'], $steps);
    }

    public function testGetController()
    {
        $result = new RoutingResult(
            true,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            [
                'a' => '11',
                'b' => '12',
                'c' => '13',
            ]
        );

        $steps = [];
        $executor = new Executor($result);
        $controller = $executor->getController();

        $this->assertInstanceOf('Controller', $controller);
    }

    public function testUnsuccessfulRoutingResult()
    {
        $result = new RoutingResult(
            false,
            false,
            false,
            [],
            ['Controller', 'dispatch'],
            []
        );

        $this->expectException(\KM\Saffron\Exception\InvalidArgument::class);
        $this->expectExceptionMessage("You cannot use unsuccessful RoutingResult to init Executor.");
        $executor = new Executor($result);
    }
}
