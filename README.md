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
Please follow to [example](https://github.com/krzysztof-magosa/saffron-php/tree/master/example)
to see how simply you can use the Saffron in your project :-)

## Setting routes

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
