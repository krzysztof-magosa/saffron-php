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

    /**
     * Checks whether specified route exists
     * 
     * @return bool
     */
    protected function hasNamedRoute($name)
    {
        return isset($this->namedRoutes[$name]);
    }

    /**
     * Appends route to router.
     * 
     * @param array $route Route to be added.
     * @return \KM\Saffron\Router $this for chaining.
     */
    public function append(array $route)
    {
        $this->prepareRoute($route);
        $this->compileRegex($route);
        $this->routes[] = $route;

        if ($route['name']) {
            if ($this->hasNamedRoute($route['name'])) {
                throw new Exception\RouteAlreadyRegistered(
                    sprintf(
                        'Route with name %s is already registered',
                        $route['name']
                    )
                );
            }
            $this->namedRoutes[$route['name']] = $route;
        }

        $this->optionalMutations($route);
        
        return $this;
    }

    protected function optionalMutations(array $route)
    {
        // Magic for optional parameters
        // Go through optional parameters, and cut off url behind them
        $placeholders = array_reverse($route['placeholders']);
        $optionalPlaceholders = array_keys($route['default']);

        foreach ($placeholders as $placeholder) {
            if (in_array($placeholder, $optionalPlaceholders)) {
                // Checks whether string ends with placeholder
                // If so gets everything before separator before it 
                // Doesn't cut of beginning char (/)
                // /product/{router} -> /product
                // /{product} -> /
                if (preg_match('@^(..*).?{'.$placeholder.'}$@Us', $route['uri'], $match)) {
                    $route['uri'] = $match[1];
                    $this->compileRegex($route);
                    $this->routes[] = $route;
                }
                else {
                    throw new Exception\InvalidRouteDefinition(
                        sprintf(
                            'It makes no sense to set default value for value %s in the middle of uri',
                            $placeholder
                        )
                    );
                }
            }
        }
    }

    /**
     * Normalizes route by adding missing fields etc.
     * 
     * @param array &route
     */
    protected function prepareRoute(array &$route)
    {
        $default = [
            'name' => null,
            'require' => [],
            'default' => [],
            'domain' => [],
            'method' => [],
            'target' => null,
        ];

        $route = array_merge($default, $route);

        $route['domain'] = (array)$route['domain'];
        $route['method'] = (array)$route['method'];
    
        preg_match_all('@{(.+)}@Us', $route['uri'], $placeholders);
        $route['placeholders'] = $placeholders[1];
    }

    /**
     * Compiles regex for gives route.
     * 
     * @param array $route Route to be compiled.
     */
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
                '(?P<'.$placeholder.'>'.$require.')',
                $regex
            );
        }

        $route['regex'] = '@^'.$regex.'$@Us';
    }

    /**
     * Dispatches the request.
     * 
     * @param \KM\Saffron\Request $request Request to be dispatched.
     * @return \KM\Saffron\MatchedRoute|null MatchedRoute object or null
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $domain = $request->getDomain();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] && !in_array($method, $route['method'])) {
                continue;
            }

            if ($route['domain'] && !in_array($domain, $route['domain'])) {
                continue;
            }

            if (!preg_match($route['regex'], $uri, $match)) {
                continue;
            }

            $matchedRoute = new MatchedRoute(
                $route['target'],
                array_merge($route['default'], $match)
            );

            return $matchedRoute;
        }
    }

    /**
     * Builds the link for named route.
     * 
     * @param string $name Name of route
     * @param array $parameters Parameters to be put into link
     * @return string Builded link
     */
    public function assemble($name, array $parameters = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new Exception\NoSuchRoute("There is no route $name.");
        }

        $uri = $this->namedRoutes[$name]['uri'];
        $defaultParameters = $this->namedRoutes[$name]['default'];
        foreach (array_merge($defaultParameters, $parameters) as $name => $value) {
            $uri = str_replace('{'.$name.'}', $value, $uri);
        }

        return $uri;
    }

    /**
     * Magic method for loading object stored by var_export
     * 
     * @return \KM\Saffron\Router
     */
    static public function __set_state($state)
    {
        $instance = new static();
        $instance->routes = $state['routes'];
        $instance->namedRoutes = $state['namedRoutes'];

        return $instance;
    }
}
