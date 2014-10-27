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

use KM\Saffron\RoutingResult;

class Executor
{
    protected $controller;
    protected $method;
    protected $parameters = [];
    protected $preDispatch;
    protected $postDispatch;

    public function __construct(RoutingResult $result = null)
    {
        if ($result) {
            $this
                ->setController($result->getTarget()[0])
                ->setMethod($result->getTarget()[1])
                ->setParameters($result->getParameters());
        }
    }

    /**
     * Sets controller to be fired.
     * It can be object or name of class.
     *
     * @param object|string $controller
     * @return Executor
     */
    public function setController($controller)
    {
        if (is_string($controller)) {
            $this->controller = new $controller();
        } else {
            $this->controller = $controller;
        }

        return $this;
    }

    /**
     * Returns controller object
     *
     * @return object
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets method to be fired
     *
     * @param string $method
     * @return Executor
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Sets parameters to be passed to controller
     *
     * @param array $parameters
     * @return Executor
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Sets callable to be called before firing controller
     *
     * @param callable $func
     * @return Executor
     */
    public function setPreDispatch(callable $func)
    {
        $this->preDispatch = $func;
        return $this;
    }

    /**
     * Sets callable to be called after firing controller
     *
     * @param callable $func
     * @return Executor
     */
    public function setPostDispatch(callable $func)
    {
        $this->postDispatch = $func;
        return $this;
    }

    /**
     * Calls hook if it's callable
     *
     * @param callable|null $hook Hook to be fired
     */
    protected function runHook($hook)
    {
        if ($hook && is_callable($hook)) {
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
            // https://bugs.php.net/bug.php?id=61384
            $name = $parameter->name;
            $arguments[] = isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        $result = $method->invokeArgs($this->controller, $arguments);

        $this->runHook($this->postDispatch);

        return $result;
    }
}
