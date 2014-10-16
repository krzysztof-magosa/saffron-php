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

class Executor
{
    protected $controller;
    protected $method;
    protected $parameters = [];
    protected $preDispatch;
    protected $postDispatch;

    public function __construct($route = null)
    {
        if ($route instanceof MatchedRoute) {
            $this
                ->setController($route->getTarget()[0])
                ->setMethod($route->getTarget()[1])
                ->setParameters($route->getParameters());
        }
    }

    public function setController($controller)
    {
        if (is_string($controller)) {
            $this->controller = new $controller();
        }
        else {
            $this->controller = $controller;
        }

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setPreDispatch(callable $func)
    {
        $this->preDispatch = $func;
        return $this;
    }

    public function setPostDispatch(callable $func)
    {
        $this->postDispatch = $func;
        return $this;
    }

    /**
     * Calls hook is it's callable
     *
     * @param callable|null $hook Hook to be fired
     */
    protected function runHook($hook)
    {
        if (is_callable($hook)) {
            call_user_func(
                $hook,
                $this->controller,
                $this->method,
                $this->parameters
            );
        }
    }

    /**
     * Runs:
     *  - preDispatch (if set)
     *  - method in controller
     *  - postDispatch (if set)
     * 
     * @return mixed Value returned by controller's method
     */
    public function fire()
    {
        $this->runHook($this->preDispatch);

        $method = new \ReflectionMethod($this->controller, $this->method);
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $arguments[] = isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        $result = $method->invokeArgs($this->controller, $arguments);

        $this->runHook($this->postDispatch);

        return $result;
    }
}
