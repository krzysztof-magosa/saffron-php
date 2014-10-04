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
class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testAutoKey()
    {
        $factory = new \KM\Saffron\RouterFactory();
        $this->assertEquals(40, strlen($factory->getCacheKey()));
    }

    public function testSetKey()
    {
        $factory = new \KM\Saffron\RouterFactory();
        $factory->setCacheKey('s9RsJpGcJ27LfUxuEWC246rY34ipUC');
        $this->assertEquals('s9RsJpGcJ27LfUxuEWC246rY34ipUC', $factory->getCacheKey());
    }

    public function testReturnedType()
    {
        $factory = new \KM\Saffron\RouterFactory();
        $router = $factory->build();
        $this->assertInstanceOf('\KM\Saffron\Router', $router);
    }
}
