<?php
$router = require __DIR__ . '/saffron/router.compiled.php';
$route = $router->dispatch(\KM\Saffron\Request::createFromGlobals());

$exec = new \KM\Saffron\Executor($route);
$exec->fire();
