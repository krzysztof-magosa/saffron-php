<?php
use KM\Saffron\Router;

$router = new Router();
$router
    ->append(
        [
            'name' => 'home',
            'uri' => '/',
            'target' => ['Site\Controller\HomeController', 'indexAction'],
        ]
    )
    ->append(
        [
            'name' => 'product',
            'uri' => '/product/{slug}/{id}',
            'require' => [
                'slug' => '\w+', // word characters (letters, numbers etc.)
                'id' => '\d+', // only numbers
            ],
            'target' => ['Site\Controller\ProductController', 'indexAction'],
        ]
    );

return $router;
