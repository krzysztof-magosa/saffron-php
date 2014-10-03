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
namespace KM\Saffron;

class Router
{
    protected $routes = [];
    protected $namedRoutes = [];

    public function append(array $route)
    {
        $this->prepareRoute($route);
        $this->compileRegex($route);
        $this->routes[] = $route;

        if ($route['name']) {
            $this->namedRoutes[$route['name']] = $route;
        }

        // Magic for optional parameters
        // Go through optional parameters, and cut off url behind them
        // @TODO protect against impossible scenarios
        foreach (array_reverse($route['placeholders']) as $placeholder) {
            if (in_array($placeholder, array_keys($route['default']))) {
                $nested = $route;
                $nested['uri'] = preg_replace(
                    '@.{0,1}\{'.$placeholder.'\}.*$@Usi',
                    '',
                    $nested['uri']
                );
                
                $this->compileRegex($nested);
                $this->routes[] = $nested;
            }
        }
        
        return $this;
    }

    protected function prepareRoute(array &$route)
    {
        if (!isset($route['name'])) {
            $route['name'] = null;
        }

        if (!isset($route['require'])) {
            $route['require'] = [];
        }

        if (!isset($route['default'])) {
            $route['default'] = [];
        }

        if (isset($route['domain'])) {
            $route['domain'] = (array)$route['domain'];
        }
        else {
            $route['domain'] = null;
        }

        if (isset($route['method'])) {
            $route['method'] = (array)$route['method'];
        }
        else {
            $route['method'] = null;
        }

        if (!isset($route['target'])) {
            $route['target'] = null;
        }

        preg_match_all('@{(.+)}@Usi', $route['uri'], $placeholders);
        $route['placeholders'] = $placeholders[1];
    }

    protected function compileRegex(array &$route)
    {
        $regex = $route['uri'];
        foreach ($route['placeholders'] as $placeholder) {
            if (isset($route['require'][$placeholder])) {
                $require = $route['require'][$placeholder];
            } else {
                $require = '.+';
            }

            $regex = str_replace(
                '{'.$placeholder .'}',
                sprintf(
                    '(?P<%s>%s)',
                    $placeholder,
                    $require
                ),
                $regex
            );
        }

        $route['regex'] = '@^'.$regex.'$@Usi';
    }

    public function dispatch(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] && !in_array($request->getMethod(), $route['method'])) {
                continue;
            }

            if ($route['domain'] && !in_array($request->getDomain(), $route['domain'])) {
                continue;
            }

            if (!preg_match($route['regex'], $request->getUri(), $match)) {
                continue;
            }

            $matchedRoute = new MatchedRoute(
                $route['target'],
                array_merge($route['default'], $match)
            );

            return $matchedRoute;
        }
    }

    public function assemble($name, array $parameters = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \LogicException("There is no route $name.");
        }

        $uri = $this->namedRoutes[$name]['uri'];
        foreach ($parameters as $name => $value) {
            $uri = str_replace('{'.$name.'}', $value, $uri);
        }

        return $uri;
    }
}
