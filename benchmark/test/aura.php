<?php
use Aura\Router\RouterFactory;

$factory = new RouterFactory;
$router = $factory->newInstance();

for ($i = 1; $i <= 9; $i++) {
    $router
        ->add('test'.$i, '/test'.$i.'/{slug}/{id}')
        ->addTokens(
            [
                'slug' => '\w+',
                'id' => '\d+',
            ]
        );
}

$router
    ->add('test', '/test/{slug}/{id}')
    ->addTokens(
        [
            'slug' => '\w+',
            'id' => '\d+',
        ]
    );

$router->match($_SERVER['REQUEST_URI'], $_SERVER);
