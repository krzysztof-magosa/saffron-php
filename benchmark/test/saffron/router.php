<?php
$router = new \KM\Saffron\Router();

for ($i = 1; $i <= 9; $i++) {
    $router->append(
        [
            'uri' => '/test'.$i.'/{slug}/{id}',
            'target' => ['Controller', 'action'],
            'require' => [
                'slug' => '\w+',
                'id' => '\d+',
            ]
        ]
    );
}

$router->append(
    [
        'uri' => '/test/{slug}/{id}',
        'target' => ['Controller', 'action'],
        'require' => [
            'slug' => '\w+',
            'id' => '\d+',
        ]
    ]
);

return $router;
