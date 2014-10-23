# Saffron PHP Router
[![Build Status](https://travis-ci.org/krzysztof-magosa/saffron-php.svg?branch=master)](https://travis-ci.org/krzysztof-magosa/saffron-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/krzysztof-magosa/saffron-php.svg)](https://coveralls.io/r/krzysztof-magosa/saffron-php?branch=master)

Router made with high performance in mind.  
Apache 2.0 licensed.

## Support
Current version of Saffron is 5.0.0 LTS.  
Previous versions were testing ground, while 5.0.0 is the first version with stable API.

## Features
* No external dependencies
* High performance
* Method condition support
* Domain condition support
* Https/non-https condition support
* Routes with optional parameters
* Requirements for parameters
* Reverse routing
* Well tested, 100% of test coverage

## Installation
You can easily install Saffron by adding below requirement to your composer.json
```json
{
    "require": {
        "krzysztof-magosa/saffron": "5.*"
    }
}
```

## Usage
I really recommend you to look into [example](https://github.com/krzysztof-magosa/saffron-php/tree/master/example)
to see how simply you can use the Saffron in your project :-)

### Initialization
Saffron accepts Request object which is matched against configured routes.
In typical cases you can use Request::createFromGlobals() to obtain instance of it.
If you have untypical configuration of web server, you can also setup this object by yourself.
```php
use KM\Saffron\Request;

// Typical initialization of Request
$request = Request::createFromGlobals();

// Custom configurations
$request = new Request();
$request
    ->setUri($uri)
    ->setDomain($domain)
    ->setMethod($method)
    ->setHttps($https);
```

To start matching requests of building links you need to initialize Router.
It can be done by RouterFactory which accepts one parameter - initialization closure.
The first parameter passed to this closure is RoutesCollection.
You just need to add your routes to this collection.

```php
use KM\Saffron\RouterFactory;

$factory = new RouterFactory(
    function ($collection) {
        $collection->route('home')
            ->setUri('/')
            ->setMethod(['POST', 'PUT'])
            ->setTarget('Site\Controller\HomeController');
    }
);
```

For readability I recommend you to store closure in separate file like in this example.
index.php
```php
$factory = new RouterFactory(require __DIR__ . '/routes.php');
```

routes.php
```php
return function ($collection) {
    $collection->route('home')
        ->setUri('/')
        ->setMethod(['POST', 'PUT'])
        ->setTarget('Site\Controller\HomeController');
};
```

This closure is fired just once and then router is compiled to optimized
PHP code and stored in cache. By default file is stored in system temporary directory.
I really encourage you to set own directory, ideally not shared with other projects.

```php
$factory->setCacheDir(__DIR__ . '/cache');
```

If you really need to share this directory across projects, or you have multiple
routers in one project, you should set suffix of filename to avoid collisions.

```php
$factory->setClassSuffix('MyProject');
```

Caching is infinite, router doesn't look for changes in the configuration after
compilation. You should take care about clearing the cache directory after deployment
of new configuration to production servers. On development environment to avoid manual
clearing of cache you can set debug mode and router will be recompiled on every request.
It of course negatively impacts performance, so before doing benchmarks
don't forget to switch to production configuration with debug mode disabled.

```php
if ($environment == 'dev') {
    $factory->setDebug(true);
}
```

When you have RouterFactory configured you can build exact Router object.

```php
$router = $factory->build();
```

### Matching request against uri (path)
#### Static uri
```php
$route->setUri('/');
$route->setUri('/contact-us');
```

#### Dynamic uri
```php
$route->setUri('/contact-with/{name}');
$route->setUri('/contact-with/{name}/by/{method}');
```

### Matching request against domain
#### Static domain
```php
$route->setDomain('www.example.com');
```

#### Dynamic domain
```php
$route->setDomain('www.example.{tld}');
```

### Matching request against http method
```php
$route->setMethod('GET');
$route->setMethod(['GET', 'POST']);
```

### Matching request against https

```php
// Allow only https traffic
$route->setHttps(true);

// Allow only non-https traffic
$route->setHttps(false);

// Https doesn't matter (default setting)
$route->setHttps(null);
```

### Setting possible values for placeholders
If you want to allow only some values to be passed to placeholders, you can restrict their values using regular expressions.
Saffron uses PERL compatible expressions (like in preg_match). All requirements are enclosed by ^ and $ internally by Saffron.
```php
$route->setRequirements(
    [
        'name' => '\w+',
        'method' => 'phone|email',
        'tld' => 'com|org',
    ]
);
```

### Setting default values for placeholders
If you want some placeholder to be optional, you can set default value for it.
It makes sense only for one or more placeholders on the end of uri, or one of more on the beggining of domain.
```php
$route->setDefaults(
    [
        'placeholder1' => 'value1',
    ]
);
```

### Setting controller to be fired by Executor
To run controller (using Executor) when route is matched you can set class and method in it.
When you omit method it takes default value 'indexAction'
```php
$route->setTarget('HomeController');
$route->setTarget('ContactController', 'emailAction');
```

## Using Executor
When request matched one of routes, you probably want to do some action.
Executor comes with easy way to do that. You just need to pass RoutingResult
to its constructor, and Executor will read all needed info from it.

```php
use KM\Saffron\Executor;

$request = ...
$facory = ...
$router = $factory->build();
$router->match($request);
$executor = new Executor($result);
$executor->fire();
```

In some cases (for example firing error controller) you may want to fire controller
which doesn't come from matched route. Executor also supports such situation.

```php
$executor = new Executor();
$executor
    ->setController('ClassName\Of\YourController')
    ->setMethod('methodName')
    ->setParameters(['param1' => 'value1'])
    ->fire();
```

You may want to do some action just before/after firing method (but after creation of controller instance).
You just need to set closure which will be fired in these cases.
Executor passes controller instance as first argument, method name as second and parameters as third.

```php
$executor = new Executor();
$executor
    ->set...
    ->setPreDispatch(
        function ($controller, $method, $parameters) {
            // do something before calling action
        }
    )
    ->setPostDispatch(
        function ($controller, $method, $parameters) {
            // do something after calling action
        }
    )
    ->fire();
```

## How to get parameters from route
Executor passes parameters from matched route into arguments of method in controller.
You need to ensure that names of arguments are the same like in configured route.
The order of arguments will be detected by Executor, you don't have to get all parameters.

```php
// ...

$collection->route('product')
    ->setUri('/product/{slug}/{id}')
    ->setTarget('Controller')
    ->setMethod('method')
    ->setRequirements(
        [
            'slug' => '\w+',
            'id' => '\d+',
        ]
    );

// ...

class Controller
{
    public function method($slug, $id)
    {
    }
}
}
```

## Checking routing status
Not all requests are matched by router. Some of them has invalid path, other invalid domain,
http status, or method. RoutingResult object gives you ability to check the situation.

```php
...
$result = $router->match($request);

if ($result->isSuccessful()) {
    // everything is fine, you can fire controller
} elseif ($result->isResourceNotFound()) {
    // Path/domain/https status is invalid, we should display error 404 here
} elseif ($result->isMethodNotAllowed()) {
    // User request action which is not allowed on this resource
    // RFC 2616 says that you should response with status 405 with Allow: header.
    // Please look into example code to see how to do minimal valid implementation.
}
```

## Building links
### Building path
1st argument is name of route.
2nd argument is array of parameters.
```php
$link = $route->assemble('name', ['parameter1' => 'value1']);
```

### Building full link
3rd parameter here means that you want full route with scheme and domain.
For routes with https condition set to true scheme will be https, for the rest http.
```php
$link = $route->assemble('name', ['parameter1' => 'value1'], true);
```
