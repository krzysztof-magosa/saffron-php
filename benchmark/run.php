<?php

require 'vendor/autoload.php';

$_SERVER['REQUEST_URI'] = '/test/slugifiedtext/123';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'example.com';

class TestController
{
    public function indexAction($slug, $id)
    {
    }
}

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
