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
require __DIR__ . '/../vendor/autoload.php';

use KM\Saffron\Request;
use KM\Saffron\Executor;
use KM\Saffron\RouterFactory;

// Build request object based on superglobals
$request = Request::createFromGlobals();

// Load routes from external file to keep this file clean
$factory = new RouterFactory(require __DIR__ . '/routes.php');

// Set parameters for factory and build router
// Make sure that web server can write to cache dir.
// If cache dir is used for many projects you are obliged
// to set unique class suffix in each of them.
$router = $factory
    ->setCacheDir(__DIR__ . '/../cache')
    ->setClassSuffix('Example')
    ->build();

// Match request against configured routes
$result = $router->match($request);

if ($result->isSuccessful()) {
    // Request matched route, run the controller
    $executor = new Executor($result);
    $executor->fire();
} else {
    $executor = new Executor();
    $executor
        ->setController('Site\\Controller\\ErrorController')
        ->setParameters(['result' => $result]);

    if ($result->isResourceNotFound()) {
        // Request didn't matched any route
        $executor
            ->setMethod('notFoundAction')
            ->fire();
    } elseif ($result->isMethodNotAllowed()) {
        // Request matched route, but method is not allowed
        $executor
            ->setMethod('methodNotAllowedAction')
            ->fire();
    }
}
