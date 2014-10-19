<?php
use \KM\Saffron\RouterFactory;
use \KM\Saffron\Executor;

$factory = new RouterFactory(
    function ($collection) {
        for ($i = 1; $i <= 9; $i++) {
            $collection->route('test'.$i)
                ->setUri('/test'.$i.'/{slug}/{id}')
                ->setTarget('\TestController')
                ->setRequirements(
                    [
                        'slug' => '\w+',
                        'id' => '\d+',
                    ]
                );
        }

        $collection->route('test')
            ->setUri('/test/{slug}/{id}')
            ->setTarget('\TestController')
            ->setRequirements(
                [
                    'slug' => '\w+',
                    'id' => '\d+',
                ]
            );
    }
);

$router = $factory
    ->setCacheDir(__DIR__ . '/../cache')
    ->setClassSuffix('SaffronExecute')
    ->build();

$route = $router->match(\KM\Saffron\Request::createFromGlobals());
$executor = new Executor($route);
$executor->fire();
