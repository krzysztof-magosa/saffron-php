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
use KM\Saffron\RoutingResult;

class RoutingResultTest extends PHPUnit_Framework_TestCase
{
    public function providerBoolean()
    {
        return [
            [true],
            [false],
        ];
    }

    public function providerMethods()
    {
        return [
            [['GET']],
            [['GET', 'POST']],
            [['GET', 'POST', 'OPTIONS']],
            [[]],
        ];
    }

    public function providerTargets()
    {
        return [
            [['Controller', 'indexAction']],
            [['HomeController', 'homeAction']],
        ];
    }

    public function providerParameters()
    {
        return [
            [[]],
            [['id' => 100,]],
            [['id' => 200, 'slug' => 'john']],
            [['id' => 300, 'slug' => null]],
            [['id' => null, 'slug' => 'john']],
        ];
    }

    /**
     * @dataProvider providerBoolean
     */
    public function testSuccessful($value)
    {
        $result = new RoutingResult();
        $result->setSuccessful($value);
        $this->assertEquals($value, $result->isSuccessful());
    }

    /**
     * @dataProvider providerBoolean
     */
    public function testMethodNotAllowed($value)
    {
        $result = new RoutingResult();
        $result->setMethodNotAllowed($value);
        $this->assertEquals($value, $result->isMethodNotAllowed());
    }

    /**
     * @dataProvider providerBoolean
     */
    public function testResourceNotFound($value)
    {
        $result = new RoutingResult();
        $result->setResourceNotFound($value);
        $this->assertEquals($value, $result->isResourceNotFound());
    }

    /**
     * @dataProvider providerMethods
     */
    public function testAllowedMethods(array $value)
    {
        $result = new RoutingResult();
        $result->setAllowedMethods($value);
        $this->assertEquals($value, $result->getAllowedMethods());
    }

    /**
     * @dataProvider providerTargets
     */
    public function testTarget(array $value)
    {
        $result = new RoutingResult();
        $result->setTarget($value);
        $this->assertEquals($value, $result->getTarget());
    }

    /**
     * @dataProvider providerParameters
     */
    public function testParameters(array $value)
    {
        $result = new RoutingResult();
        $result->setParameters($value);
        $this->assertEquals($value, $result->getParameters());
    }
}
