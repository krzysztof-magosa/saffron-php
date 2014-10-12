<?php
$mux = new \Pux\Mux();

for ($i = 1; $i <= 9; $i++) {
    $mux->any(
        '/test'.$i.'/:slug/:id',
        ['Controller', 'action'],
        [
            'require' => [
                'slug' => '\w+',
                'id' => '\d+',
            ],
        ]
    );
}

$mux->any(
    '/test/:slug/:id',
    ['Controller', 'action'],
    [
        'require' => [
            'slug' => '\w+',
            'id' => '\d+',
        ],
    ]
);

$mux->dispatch($_SERVER['REQUEST_URI']);
