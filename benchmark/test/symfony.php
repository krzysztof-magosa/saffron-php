<?php

$closure = function() {
    $routes = new \Symfony\Component\Routing\RouteCollection();

    for ($i = 1; $i <= 9; $i++) {
        $routes->add(
            'test'.$i,
            new \Symfony\Component\Routing\Route(
                '/test'.$i.'/{slug}/{id}',
                array('_controller' => 'XBundle:Controller:foo'),
                array(
                    'slug' => '\w+',
                    'id' => '\d+',
                )
            )
        );
    }

    $routes->add(
        'test',
        new \Symfony\Component\Routing\Route(
            '/test/{slug}/{id}',
            array('_controller' => 'XBundle:Controller:foo'),
            array(
                'slug' => '\w+',
                'id' => '\d+',
            )
        )
    );

    return $routes;
};

$loader = new Symfony\Component\Routing\Loader\ClosureLoader();
$requestContext = new \Symfony\Component\Routing\RequestContext($_SERVER['REQUEST_URI']);
$router = new \Symfony\Component\Routing\Router(
    $loader,
    $closure,
    array(
        'cache_dir' => sys_get_temp_dir() . '/router_cache'
    ),
    $requestContext
);


$route = $router->match($_SERVER['REQUEST_URI']);
