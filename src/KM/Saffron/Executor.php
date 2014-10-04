<?php
namespace KM\Saffron;

class Executor
{
    static public function executeController($controllerName, $actionName, $parameters)
    {
        $controller = new $controllerName();

        $reflection = new \ReflectionClass($controllerName);
        $method = $reflection->getMethod($actionName);
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->getParam($parameter->getName(), null);
        }

        $method->invokeArgs($controller, $arguments);

        return $controller;
    }
}
