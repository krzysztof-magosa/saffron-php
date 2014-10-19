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
use KM\Saffron\RouterFactory;

class RoutesFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCacheDir()
    {
        $factory = new RouterFactory(function () {});
        $this->assertEquals(sys_get_temp_dir(), $factory->getCacheDir());
        $factory->setCacheDir('/cache/dir');
        $this->assertEquals('/cache/dir', $factory->getCacheDir());
    }

    public function testClassSuffix()
    {
        $factory = new RouterFactory(function () {});
        $this->assertEquals('', $factory->getClassSuffix());
        $factory->setDebug(true);
        $this->assertEquals(32, strlen($factory->getClassSuffix()));
        $factory->setClassSuffix('hello');
        $this->assertEquals(37, strlen($factory->getClassSuffix()));
        $this->assertEquals('hello', substr($factory->getClassSuffix(), 0, 5));
    }
}
