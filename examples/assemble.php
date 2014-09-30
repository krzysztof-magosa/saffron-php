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
require_once __DIR__  . '/../vendor/autoload.php';

use KM\Saffron\Request;
use KM\Saffron\Router;

// Configure routes
$router = new Router();
$router     
    ->append(
        [
            'name' => 'show',
            'uri' => '/show/{entity}/{id}',
            'require' => [
                'entity' => '\w+',
                'id' => '\d+',
            ],
            'target' => function ($matched) {}
        ]
    )
    ->append(
        [
            'name' => 'edit',
            'uri' => '/edit/{entity}/{id}',
            'require' => [
                'entity' => '\w+',
                'id' => '\d+',
            ],
            'target' => ['Controller', 'editAction'],
        ]
    );

// Build link
$link = $router->assemble(
    'show',
    [
        'entity' => 'example',
        'id' => 123,
    ]
);

echo "Generated link: $link\n";
