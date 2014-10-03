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

class MatchedRoute
{
    protected $target;
    protected $parameters;

    public function __construct($target, array $parameters)
    {
        $this->target = $target;
        $this->parameters = $parameters;
    }

    public function getParam($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    protected function executeClosure()
    {
        $closure = $this->target;
        $closure($this);
    }

    protected function executeController()
    {
        $controllerName = $this->target[0];
        $actionName = $this->target[1];

        $controller = new $controllerName();

        $reflection = new \ReflectionClass($controllerName);
        $method = $reflection->getMethod($actionName);
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->getParam($parameter->getName(), null);
        }

        $method->invokeArgs($controller, $arguments);
    }

    public function execute()
    {
        if ($this->target instanceof \Closure) {
            $this->executeClosure();
        }
        else {
            $this->executeController();
        }
    }
}
