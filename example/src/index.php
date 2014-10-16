<?php
require __DIR__ . '/../vendor/autoload.php';

use KM\Saffron\Request;
use KM\Saffron\Executor;
use KM\Saffron\RouterFactory;

$factory = new RouterFactory(
    function ($collection) {
        $collection->route('home')
            ->setUri('/')
            ->setTarget('Site\Controller\HomeController');

        $collection->route('product')
            ->setUri('/product/{slug}/{id}')
            ->setTarget('Site\Controller\ProductController')
            ->setRequires(
                [
                    'slug' => '\w+',
                    'id' => '\d+',
                ]
            );
    }
);

$router = $factory->build();
$route = $router->match(\KM\Saffron\Request::createFromGlobals());

if ($route) {
    $executor = new Executor($route);
    $executor->fire();
}
else {
    echo 'Error 404';
}
