<?php
use \KM\Saffron\RouterFactory;

$factory = new RouterFactory(
    function ($collection) {
        for ($i = 1; $i <= 9; $i++) {
            $collection->route('test'.$i)
                ->setUri('/test'.$i.'/{slug}/{id}')
                ->setTarget('TestController')
                ->setRequires(
                    [
                        'slug' => '\w+',
                        'id' => '\d+',
                    ]
                );
        }

        $collection->route('test')
            ->setUri('/test/{slug}/{id}')
            ->setTarget('TestController')
            ->setRequires(
                [
                    'slug' => '\w+',
                    'id' => '\d+',
                ]
            );
    }
);

$router = $factory
    ->build();

$router->match(\KM\Saffron\Request::createFromGlobals());
