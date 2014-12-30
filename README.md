Saffron PHP Router
==================
[![Build Status](https://travis-ci.org/krzysztof-magosa/saffron-php.svg?branch=master)](https://travis-ci.org/krzysztof-magosa/saffron-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/krzysztof-magosa/saffron-php.svg)](https://coveralls.io/r/krzysztof-magosa/saffron-php?branch=master)

* [What is Saffron](#what-is-saffron)
* [Version](#version)
* [Features](#features)
* [Installation](#installation)
* [How to use](#how-to-use)
* [Configuring routes](#configuring-routes)
 * [Setting target](#setting-target)
 * [Setting uri](#setting-uri)
 * [Setting domain](#setting-domain)
 * [Setting method](#setting-method)
 * [Setting https](#setting-https)
 * [Using placeholders](#using-placeholders)
* [Matching requests](#matching-requests)
* [Building links](#building-links)
* [Executing controllers](#executing-controllers)
* [Getting parameters](#getting-parameters)
* [Example](https://github.com/krzysztof-magosa/saffron-php/tree/master/example)

## What is Saffron?
Saffron is very fast and flexible PHP router for your application.

## Version
The current version of Saffron is 5.0.0 LTS.  
It will be maintained at least till 31st December of 2015.

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

## How to use
You need to use RouterFactory to create instance of Router.
Constructor of RouterFactory accepts one parameter, Closure which configures routes. Closure gets RoutesCollection in the first parameter. Closure is fired only once, then everything is stored in compiled file.
```php
use KM\Saffron\RouterFactory;

$factory = new RouterFactory(
    function ($collection) {
        // configuration of routes goes here...
        $collection->route('home')
            ->setUri('/')
            ->setTarget('HomeController');
    }
);
```
By default Saffron stores cache in system temporary directory.
To avoid collisions between projects you are encouraged to set separate cache directories in each project hosted on the same server. If you really need to use one directory for more projects you can set class suffix. 
```php
$factory
    ->setCacheDir(__DIR__ . '/cache')
    ->setClassSuffix('MyProject')
    ->build();
```
When you have configured RouterFactory, you can build Router instance by calling build() method.
```php
$router = $factory->build();
```
## Configuring routes
```php
use KM\Saffron\RouterFactory;

$factory = new RouterFactory(
    function ($collection) {
        $collection->route('home')
            ->setUri('/')
            ->setTarget('HomeController');
            
        $collection->route('contact')
            ->setUri('/contact')
            //...
            ->setTarget('ContactController');
            
        //...
    }
);
```
To add Route you need to call method route() on $collection, giving route name as the first parameter. Method returns Route instance and then you can set parameters on it. You can create as many routes as you want, but each one needs to have unique name.

### Setting target
To execute controller after matching particular route you need to use setTarget() method. First parameter is class name of controller, second is method name. If you omit second parameter it will default to 'indexAction'.

```php
$collection->route('home')
    ->setTarget('HomeController');

$collection->route('team')
    ->setTarget('TeamController', 'actionName');
```

### Setting uri
To match request against uri you need to call method setUri() on Route instance. It takes only one parameter, expected uri.
```php
$collection->route('contact')
    ->setUri('/contact');
```

### Setting domain
To match request against domain you need to call method setDomain() on Route instance. It takes only one parameter, expected domain.
```php
$collection->route('contact')
    ->setDomain('www.example.com');
```

### Setting method
To match request against method you need to call method setMethod() on Route instance. You can pass one method as a string, or more using array.
```php
$collection->route('api1')
    ->setMethod('GET');

$collection->route('api2')
    ->setMethod(['GET', 'POST']);
```

### Setting https
You may want to allow access to some resources only via encrypted or unencrypted connection. It can be done using setHttps() method. Pass true to this method if you want only encrypted traffic, false if unecrypted. Null means that it doesn't matter (it's the default setting).

```php
$collection->route('secret')
    ->setHttps(true);
    
$collection->route('public')
    ->setHttps(false);
```

### Using placeholders
If your uri or domain contains variable parts, you can catch them using placeholders. Placeholders are defined using curly braces.
```php
$collection->route('contact')
    ->setUri('/contact/{name}')
    ->setDomain('{lang}.example.com');
```
This example allows you to use links like:
* http://english.example.com/contact/webmaster
* http://spanish.example.com/contact/author

Sometimes you want to allow user to omit some placeholders in the link.
You can use setDefaults() method to achieve this.
```php
$collection->route('contact')
    ->setUri('/contact/{name}')
    ->setDomain('{lang}.example.com')
    ->setDefaults(
        [
            'name' => 'webmaster',
            'lang' => 'english',
        ]
    );
```
Now user can go into link http://example.com/contact, and lang will be 'english', and name will be 'webmaster'.

You can also set requirements for placeholders, to allow user only to use some values there. Requirements are build using regular expressions, the same which you use in the preg_match(). 
```php
$collection->route('contact')
    ->setUri('/contact/{name}')
    ->setDomain('{lang}.example.com')
    ->setDefaults(
        [
            'name' => 'webmaster',
            'lang' => 'english',
        ]
    )
    ->setRequirements(
        [
            'name' => '\w+',
            'lang' => 'english|spanish|french',
        ]
    );
```

## Matching requests
Saffron accepts Request object. In typical configurations you can use createFromGlobals() static method. It was tested on Apache server with mod_php.
```php
use KM\Saffron\Request;
$request = Request::createFromGlobals();
```
If your configuration isn't typical, you can create this object manually.
```php
use KM\Saffron\Request;
$request = new Request();
$request
    ->setUri($uri)
    ->setDomain($domain)
    ->setMethod($method)
    ->setHttps($https);
```

Now you can pass this object to Router match() method which returns RoutingResult object.
```php
$result = $router->match($request);
```
You can check whether matching was successful using isSuccessful() method. To check why matching was not successful you need to use two methods: isResourceNotFound() and isMethodNotAllowed(). When isResourceNotFound() returns true you should display error 404 to user, when isMethodNotAllowed() returns true you should display error 405. RFC 2616 requires to set Allow header containing allowed methods in case of displaying 405 error. You can get this list by calling getAllowedMethods(). Remembet that Saffron is not framework, but just router, so it's up to you to do that.

## Building links
Saffron is two-directional router, so in addition to matching requests you can also build links. Router has method assemble() for building links.
```php
// Building uri
$uri = $router->assemble('routeName', ['parameter1' => 'value1']);

// Building entire link (scheme + domain + uri)
$link = $router->assemble('routeName', ['parameter1' => 'value1'], true);
```

## Executing controllers
After successful matching of request you can fire controller specified in the route using Executor.
```php
use KM\Saffron\Executor;
$executor = new Executor($result);
$executor->fire();
```
In some cases there is need to do something with controller just before or/and just after executing action. It can be done by using setPreDispatch() and setPostDispatch().
```php
use KM\Saffron\Executor;
$executor = new Executor($result);
$executor
    ->setPreDispatch(
        function ($controller, $method, $parameters) {
            // do something before calling action
        }
    )
    ->setPostDispatch(
        function ($controller, $method, $parameters) {
            // do something after calling action
        }
    );

$executor->fire();
```


In some cases (for example firing error controller) you may want to fire controller which doesn't come from matched route. Executor also supports such situation.
```php
$executor = new Executor();
$executor
    ->setController('YourController')
    ->setMethod('methodName')
    ->setParameters(['param1' => 'value1'])
    ->fire();
```

## Getting parameters
If your uri or domain containts placeholders they will be passed to your controller in arguments to method. You just need to ensure that controller method has arguments with the same names as your placeholders. You don't have to catch all placeholders.

```php
class Controller
{
    public function method($lang, $name)
    {
    }
}
```
