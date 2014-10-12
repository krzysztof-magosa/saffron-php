<?php
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

$context = new \Symfony\Component\Routing\RequestContext($_SERVER['REQUEST_URI']);
$matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);
$route = $matcher->match($_SERVER['REQUEST_URI']);
