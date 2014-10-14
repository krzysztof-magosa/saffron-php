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
        $router = new \KM\Saffron\Router();
        $router
            ->append(
                [
                    'name' => 'create',
                    'uri' => '/product',
                    'method' => 'POST',
                    'domain' => 'example1.com',
                    'target' => ['ProductController', 'createAction'],
                    'default' => ['routeName' => 'create'],
                ]
            )
            ->append(
                [
                    'name' => 'update',
                    'uri' => '/product/{id}',
                    'method' => 'POST',
                    'domain' => 'example2.com',
                    'target' => ['ProductController', 'updateAction'],
                    'default' => ['routeName' => 'update'],
                ]
            )
            ->append(
                [
                    'name' => 'get',
                    'uri' => '/product/{id}',
                    'method' => 'GET',
                    'domain' => 'example3.com',
                    'target' => ['ProductController', 'getAction'],
                    'default' => ['routeName' => 'get'],
                ]
            )
            ->append(
                [
                    'name' => 'delete',
                    'uri' => '/product/{id}',
                    'method' => 'DELETE',
                    'domain' => 'example4.com',
                    'target' => ['ProductController', 'deleteAction'],
                    'default' => ['routeName' => 'delete'],
                ]
            )
            ->append(
                [
                    'name' => 'put',
                    'uri' => '/product/{id}',
                    'method' => 'PUT',
                    'domain' => 'example5.com',
                    'https' => true,
                    'target' => ['ProductController', 'deleteAction'],
                    'default' => ['routeName' => 'put'],
                ]
            );

        $request = new KM\Saffron\Request();
        $request
            ->setUri($uri)
            ->setMethod($method)
            ->setDomain($domain)
            ->setHttps($https);

        $route = $router->dispatch($request);
        
        $this->assertEquals($expected, $route->getParam('routeName'));
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
        $router = new \KM\Saffron\Router();
        $router
            ->append(
                [
                    'name' => 'get',
                    'uri' => $pattern,
                    'method' => 'GET',
                    'target' => ['ProductController', 'getAction'],
                    'default' => ['id' => 12345],
                ]
            );

        $request = new KM\Saffron\Request();
        $request
            ->setUri($uri)
            ->setMethod('GET');

        $route = $router->dispatch($request);
        
        $this->assertEquals(12345, $route->getParam('id'));
    }

    /**
     * @expectedException \KM\Saffron\Exception\RouteAlreadyRegistered
     * @expectedExceptionMessage Route with name home is already registered
     */
    public function testDuplicateOfNamedRoute()
    {
        $router = new \KM\Saffron\Router();
        $router->append(['name' => 'home', 'uri' => '/']);
        $router->append(['name' => 'home', 'uri' => '/home']);
    }

    /**
     * @expectedException \KM\Saffron\Exception\InvalidRouteDefinition
     * @expectedExceptionMessage It makes no sense to set default value for value place in the middle of uri
     */
    public function testDefaultValueInMiddle()
    {
        $router = new \KM\Saffron\Router();
        $router->append(
            [
                'uri' => '/something/{place}/somethingElse',
                'default' => [
                    'place' => 'defaultValue',
                ]
            ]
        );
    }

    public function testAssemble()
    {
        $router = new \KM\Saffron\Router();
        $router->append(
            [
                'name' => 'contact',
                'uri' => '/contact/{name}'
            ]
        );

        $this->assertEquals('/contact/km', $router->assemble('contact', ['name' => 'km']));
    }

    /**
     * @expectedException \KM\Saffron\Exception\NoSuchRoute
     * @expectedExceptionMessage There is no route home.
     */
    public function testAssembleNoSuchRoute()
    {
        $router = new \KM\Saffron\Router();
        $router->append(
            [
                'name' => 'contact',
                'uri' => '/contact/{name}'
            ]
        );

        $this->assertEquals('/contact/km', $router->assemble('home'));
    }

    public function testRequireRegex1()
    {
        $router = new \KM\Saffron\Router();
        $router
            ->append(
                [
                    'name' => 'team',
                    'uri' => '/team/{name}/{id}',
                    'target' => ['TeamController', 'dispatch'],
                    'require' => [
                        'id' => '\d+'
                    ]
                ]
            );
            
        $request = new KM\Saffron\Request();
        $request->setUri('/team/superteam/digit');
        $route = $router->dispatch($request);

        $this->assertNull($route);
    }

    public function testRequireRegex2()
    {
        $router = new \KM\Saffron\Router();
        $router
            ->append(
                [
                    'name' => 'team',
                    'uri' => '/team/{name}/{id}',
                    'target' => ['TeamController', 'dispatch'],
                    'require' => [
                        'id' => '\d+'
                    ]
                ]
            );
            
        $request = new KM\Saffron\Request();
        $request->setUri('/team/superteam/5');
        $route = $router->dispatch($request);

        $this->assertNotNull($route);
        $this->assertInstanceOf('\KM\Saffron\MatchedRoute', $route);
    }

    public function testSetState()
    {
        $router1 = new \KM\Saffron\Router();
        $router1
            ->append(
                [
                    'name' => 'team',
                    'uri' => '/team/{name}/{id}',
                    'target' => ['TeamController', 'dispatch'],
                    'require' => [
                        'id' => '\d+'
                    ]
                ]
            );

        $state = var_export($router1, true);
        $router2 = eval('return ' . $state .';');

        $this->assertEquals($router1, $router2);
    }
}
