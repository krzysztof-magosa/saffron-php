# Saffron PHP Router
[![Build Status](https://travis-ci.org/krzysztof-magosa/saffron-php.svg?branch=master)](https://travis-ci.org/krzysztof-magosa/saffron-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/krzysztof-magosa/saffron-php/?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/krzysztof-magosa/saffron-php.svg)](https://coveralls.io/r/krzysztof-magosa/saffron-php?branch=master)

Router made with high performance in mind.  
Free for personal and commercial usage.

## Features
* No dependencies
* High performance
* Method condition support
* Domain condition support
* Routes with optional parameters
* Reverse routing

## Installation
You can easily install Saffron by adding below requirement to your composer.json
```json
{
    "require": {
        "krzysztof-magosa/saffron": "4.*"
    }
}
```

## Performance
<pre>
  Saffron (c)  3.79 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
Saffron (e+c)  3.24 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
      Saffron  2.05 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
  Saffron (e)  1.74 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
    Klein (e)  1.36 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
          Pux  1.22 k/s ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
      Symfony  0.64 k/s ▒▒▒▒▒▒▒▒▒
         Aura  0.55 k/s ▒
</pre>

* c - router uses "precompilation" (var_export) of objects to speed up start
* e - router executes Controller or provided Closure
* no letter - router just returns matched route

Benchmark performed on OS X Yosemite, CPU i5 2.6GHz, PHP 5.6.1, in CLI.  
There were 10 regex routes, last one was triggered.  
Pux was used without its module for PHP.  

## Usage
Please follow to [example](https://github.com/krzysztof-magosa/saffron-php/tree/master/example) 
to see how simply you can use the Saffron in your project :-)
