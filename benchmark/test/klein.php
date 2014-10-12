<?php
$klein = new \Klein\Klein();

for ($i = 1; $i <= 9; $i++) {
    $klein->respond(
        'GET',
        '/test'.$i.'/[a:slug]/[i:id]',
        function () {
        }
    );
}

$klein->respond(
    'GET',
    '/test/[a:slug]/[i:id]',
    function () {
    }
);

$klein->dispatch();
