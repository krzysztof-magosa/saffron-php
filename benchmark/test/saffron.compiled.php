<?php
$router = require __DIR__ . '/saffron/router.compiled.php';
$router->dispatch(\KM\Saffron\Request::createFromGlobals());
