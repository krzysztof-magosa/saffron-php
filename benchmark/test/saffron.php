<?php
$router = require __DIR__ . '/saffron/router.php';
$router->dispatch(\KM\Saffron\Request::createFromGlobals());
