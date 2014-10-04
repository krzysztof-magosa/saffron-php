# Saffron PHP Router

Saffron is a tiny router for your PHP applications.  
The main goal is to make it as simple as it can be.

## Features
* No dependencies
* High performance
* Method condition support
* Domain condition support
* Routes with optional parameters

## Installation
You can easily install Saffron by adding below requirement to your composer.json
```json
{
    "require": {
        "krzysztof-magosa/saffron": "0.2.*"
    }
}
```

## Example
```php
require 'vendor/autoload.php';

use KM\Saffron\Request;
use KM\Saffron\RouterFactory;

// Configure routing
$factory = new RouterFactory();
    ->setRoutes(
        [
            [
                'name' => 'show',
                'uri' => '/show/{entity}/{id}',
                'require' => [
                    'entity' => '\w+',
                    'id' => '\d+',
                ],
                'default' => [
                    'id' => 123,
                ],
                'target' => ['Controller', 'showAction'],
            ],
        ]
    );

$router = $factory->build();

// Create Request object
$request = new Request();
$request
    ->setUri(explode('?', $_SERVER['REQUEST_URI'])[0]) // remove possible query string
    ->setMethod($_SERVER['REQUEST_METHOD']);

// Dispatch request
$route = $router->dispatch($request);

if ($route) {
    // Execute controller if route matched
    $route->execute();
}
else {
    // Handle error 404
}
```

```php
class Controller
{
    public function showAction($entity, $id)
    {
        echo "I would show $entity with id $id";
    }
}
```
