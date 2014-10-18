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
use KM\Saffron\Request;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function providerAssemble()
    {
        return [
            [
                'uri' => '/person/{name}/{id}',
                'defaults' => ['id' => 5],
                'parameters' => ['name' => 'john'],
                'result' => '/person/john/5',
            ],
            [
                'uri' => '/person/{name}',
                'defaults' => [],
                'parameters' => ['name' => 'john'],
                'result' => '/person/john',
            ],
            [
                'uri' => '/person/{name}',
                'defaults' => ['name' => 'john'],
                'parameters' => [],
                'result' => '/person/john',
            ],
            [
                'uri' => '/{module}/{name}',
                'defaults' => ['name' => 'john'],
                'parameters' => ['module' => 'person'],
                'result' => '/person/john',
            ],
        ];
    }

    public function providerMatch()
    {
        return [
            ['/home', 'www.test99.com', 'GET', false, false, false, [], ['route' => 'test99']],
            ['/home', 'www.test1.com', 'GET', false, false, false, [], ['route' => 'test1']],
            ['/home', 'www.test1.com', 'POST', false, false, false, [], ['route' => 'test2']],

            ['/person/john', 'www.test3.com', 'GET', null, false, false, [], ['route' => 'test3', 'slug' => 'john']],
            ['/person', 'www.test3.com', 'GET', false, false, false, [], ['route' => 'test3', 'slug' => 'jack']],

            ['/account', 'www.test4.com', 'GET', true, false, false, [], ['route' => 'test4a']],
            ['/account', 'www.test4.com', 'GET', false, false, false, [], ['route' => 'test4b']],

            ['/info/5', 'www.test5.com', 'GET', false, false, false, [], ['route' => 'test5a', 'id' => 5]],
            ['/info/news', 'www.test5.com', 'GET', false, false, false, [], ['route' => 'test5b', 'slug' => 'news']],

            ['/methods/get', 'www.test6.com', 'POST', false, false, true, ['GET'], []],
            ['/not-found', 'www.test7.com', 'GET', false, true, false, [], []],

            ['/test100', 'www.test100.com', 'GET', false, false, false, [], []],
        ];
    }

    /**
     * @dataProvider providerMatch
     */
    public function testMatch($uri, $domain, $method, $https, $expectedResourceNotFound, $expectedMethodNotAllowed, array $expectedAllowedMethods, array $expectedParameters)
    {
        $factory = new RouterFactory(
            function ($collection) {
                $collection->route('test1')
                    ->setUri('/home')
                    ->setDomain('www.test1.com')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test1']);

                $collection->route('test2')
                    ->setUri('/home')
                    ->setDomain('www.test1.com')
                    ->setMethod('POST')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test2']);

                $collection->route('test3')
                    ->setUri('/person/{slug}')
                    ->setDomain('www.test3.com')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test3', 'slug' => 'jack']);

                $collection->route('test4a')
                    ->setUri('/account')
                    ->setDomain('www.test4.com')
                    ->setMethod('GET')
                    ->setHttps(true)
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test4a']);

                $collection->route('test4b')
                    ->setUri('/account')
                    ->setDomain('www.test4.com')
                    ->setMethod('GET')
                    ->setHttps(false)
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test4b']);

                $collection->route('test5a')
                    ->setUri('/info/{id}')
                    ->setDomain('www.test5.com')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test5a'])
                    ->setRequires(['id' => '\d+']);

                $collection->route('test5b')
                    ->setUri('/info/{slug}')
                    ->setDomain('www.test5.com')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test5b'])
                    ->setRequires(['slug' => '\w+']);

                $collection->route('test6')
                    ->setUri('/methods/get')
                    ->setDomain('www.test6.com')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction');

                $collection->route('test99')
                    ->setUri('/home')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction')
                    ->setDefaults(['route' => 'test99']);

                $collection->route('test100')
                    ->setUri('/test100')
                    ->setMethod('GET')
                    ->setTarget('TestController', 'createAction');
            }
        );

        $factory
            ->setDebug(true)
            ->build();

        $request = new Request();
        $request
            ->setUri($uri)
            ->setDomain($domain)
            ->setMethod($method)
            ->setHttps($https);

        $result = $factory->build()->match($request);
        $this->assertEquals($expectedResourceNotFound, $result->isResourceNotFound());
        $this->assertEquals($expectedMethodNotAllowed, $result->isMethodNotAllowed());
        $this->assertEquals($expectedAllowedMethods, $result->getAllowedMethods());
        $this->assertEquals($expectedParameters, $result->getParameters());
    }

    /**
     * @dataProvider providerAssemble
     */
    public function testAssemble($uri, $defaults, $parameters, $result)
    {
        $factory = new RouterFactory(
            function ($collection) use ($uri, $defaults) {
                $collection->route('test')
                    ->setUri($uri)
                    ->setDefaults($defaults);
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $this->assertEquals(
            $result,
            $router->assemble('test', $parameters)
        );
    }

    /**
     * @expectedException \KM\Saffron\Exception\NoSuchRoute
     * @expectedExceptionMessage There is no route home.
     */
    public function testAssembleNoSuchRoute()
    {
        $factory = new RouterFactory(
            function ($collection) {
                $collection->route('contact')
                    ->setUri('/contact/{name}');
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $this->assertEquals('/contact/km', $router->assemble('home'));
    }
}
