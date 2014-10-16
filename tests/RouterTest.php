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
class RouterTest extends PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            ['/product', 'example1.com', 'POST', false, 'create'],
            ['/product/100', 'example2.com', 'POST', false, 'update'],
            ['/product/200', 'example3.com', 'GET', false, 'get'],
            ['/product/300', 'example4.com', 'DELETE', false, 'delete'],
            ['/product/400', 'example5.com', 'PUT', true, 'put'],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testDispatch($uri, $domain, $method, $https, $expected)
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) {
                $collection->route('create')
                    ->setUri('/product')
                    ->setMethod('POST')
                    ->setDomain('example1.com')
                    ->setTarget('ProductController', 'createAction')
                    ->setDefaults(['routeName' => 'create']);
                        
                $collection->route('update')
                    ->setUri('/product/{id}')
                    ->setMethod('POST')
                    ->setDomain('example2.com')
                    ->setTarget('ProductController', 'updateAction')
                    ->setDefaults(['routeName' => 'update']);

                $collection->route('get')
                    ->setUri('/product/{id}')
                    ->setMethod('GET')
                    ->setDomain('example3.com')
                    ->setTarget('ProductController', 'getAction')
                    ->setDefaults(['routeName' => 'get']);
                    
                $collection->route('delete')
                    ->setUri('/product/{id}')
                    ->setMethod('DELETE')
                    ->setDomain('example4.com')
                    ->setTarget('ProductController', 'deleteAction')
                    ->setDefaults(['routeName' => 'delete']);

                $collection->route('put')
                    ->setUri('/product/{id}')
                    ->setMethod('PUT')
                    ->setDomain('example5.com')
                    ->setTarget('ProductController', 'deleteAction')
                    ->setDefaults(['routeName' => 'put']);
            }
        );

        $factory
            ->setDebug(true)
            ->build();

        $request = new KM\Saffron\Request();
        $request
            ->setUri($uri)
            ->setMethod($method)
            ->setDomain($domain)
            ->setHttps($https);

        $route = $factory->build()->match($request);
        $this->assertEquals($expected, $route->getParameter('routeName'));
    }

    public function optionalProvider()
    {
        return [
            [ '/product/{id}', '/product' ],
            [ '/{id}', '/' ]
        ];
    }

    /**
     * @dataProvider optionalProvider
     */
    public function testOptionalParam($pattern, $uri)
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) use ($pattern) {
                $collection->route('get')
                    ->setUri($pattern)
                    ->setMethod('GET')
                    ->setDefaults(['id' => 12345]);
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $request = new KM\Saffron\Request();
        $request
            ->setUri($uri)
            ->setMethod('GET');

        $route = $router->match($request);
        $this->assertEquals(12345, $route->getParameter('id'));
    }

    /**
     * @expectedException \KM\Saffron\Exception\RouteAlreadyRegistered
     * @expectedExceptionMessage Route with name home is already registered
     */
    public function testDuplicateOfNamedRoute()
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) {
                $collection->route('home')
                    ->setUri('/');

                $collection->route('home')
                    ->setUri('/home');
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $request = new \KM\Saffron\Request();
        $request->setUri('/test');

        $router->match($request);
    }

    public function testAssemble()
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) {
                $collection->route('contact')
                    ->setUri('/contact/{name}');
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $this->assertEquals('/contact/km', $router->assemble('contact', ['name' => 'km']));
    }

    /**
     * @expectedException \KM\Saffron\Exception\NoSuchRoute
     * @expectedExceptionMessage There is no route home.
     */
    public function testAssembleNoSuchRoute()
    {
        $factory = new \KM\Saffron\RouterFactory(
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

    public function testRequireRegex1()
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) {
                $collection->route('team')
                    ->setUri('/team/{name}/{id}')
                    ->setRequires(['id' => '\d+']);
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();
            
        $request = new KM\Saffron\Request();
        $request->setUri('/team/superteam/digit');
        $route = $router->match($request);

        $this->assertNull($route);
    }

    public function testRequireRegex2()
    {
        $factory = new \KM\Saffron\RouterFactory(
            function ($collection) {
                $collection->route('team')
                    ->setUri('/team/{name}/{id}')
                    ->setRequires(['id' => '\d+']);
            }
        );

        $router = $factory
            ->setDebug(true)
            ->build();

        $request = new \KM\Saffron\Request();
        $request->setUri('/team/superteam/5');
        $route = $router->match($request);

        $this->assertNotNull($route);
        $this->assertInstanceOf('\KM\Saffron\MatchedRoute', $route);
    }
}
