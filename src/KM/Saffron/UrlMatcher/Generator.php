<?php
/**
 * Copyright 2014 Krzysztof Magosa
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace KM\Saffron\UrlMatcher;

use KM\Saffron\RoutesCollection;
use KM\Saffron\Route;

class Generator extends \KM\Saffron\Generator
{
    protected $collection;

    public function __construct(RoutesCollection $collection)
    {
        $this->collection = $collection;
    }

    protected function compileRoute(Route $route)
    {
        $cond[] = sprintf(
            '0 === strpos($uri, %s)',
            var_export($route->getPrefix(), true)
        );

        if ($route->needsRegex()) {
            $cond[] = sprintf(
                'preg_match(%s, $uri, $match)',
                var_export($route->getRegex(), true)
            );
        }
        else {
            $cond[] = sprintf(
                '%d === strlen($uri)',
                strlen($route->getPrefix())
            );
        }

        if ($route->hasMethod()) {
            $cond[] = sprintf(
                'in_array($method, %s)',
                $this->formatArray($route->getMethod())
            );
        }

        if ($route->hasDomain()) {
            $cond[] = sprintf(
                'in_array($domain, %s)',
                $this->formatArray($route->getDomain())
            );
        }

        $conditions = implode(' && ', $cond);
        $target = $this->formatArray($route->getTarget());
        $defaults = $this->formatArray($route->getDefaults());

        if ($route->needsRegex()) {
            return <<<EOB
        if ($conditions) {
            return new MatchedRoute(
                $target,
                array_replace($defaults, \$match)
            );
        }
EOB;
        }
        else {
            return <<<EOB
        if ($conditions) {
            return new MatchedRoute(
                $target,
                $defaults
            );
        }
EOB;
        }
    }

    protected function expandRoutes()
    {
        $routes = [];

        foreach ($this->collection as $route) {
            $routes[] = $this->compileRoute($route);
        }

        return implode("\n", $routes);
    }

    public function generate($className)
    {
        return <<<EOB
<?php
use KM\Saffron\UrlMatcher;
use KM\Saffron\Request;
use KM\Saffron\MatchedRoute;
use KM\Saffron\UrlMatcher\Base;

class $className extends Base
{
    public function match(Request \$request)
    {
        \$method = \$request->getMethod();
        \$domain = \$request->getDomain();
        \$uri = \$request->getUri();

{$this->expandRoutes()}
    }
}

EOB;
    }
}
