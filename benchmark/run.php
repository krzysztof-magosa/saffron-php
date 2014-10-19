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
require 'vendor/autoload.php';

class TestController
{
    public function indexAction($slug, $id)
    {
    }
}

$_SERVER['REQUEST_URI'] = '/test/slugifiedtext/123';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'example.com';

// Clear cache
array_map('unlink', glob(__DIR__ . '/cache/*.php'));

$bench = new \KM\Benchmark();
$bench
    ->execute(
       'Saffron',
        function () {
            require 'test/saffron.php';
        }
    )
    ->execute(
       'Saffron (e)',
        function () {
            require 'test/saffron.execute.php';
        }
    )
    ->execute(
        'Pux',
        function () {
            require 'test/pux.php';
        }
    )
    ->execute(
        'Symfony',
        function () {
            require 'test/symfony.php';
        }
    )
    ->execute(
        'Klein (e)',
        function () {
            require 'test/klein.php';
        }
    )
    ->execute(
        'Aura',
        function () {
            require 'test/aura.php';
        }
    )
    ->summary();
