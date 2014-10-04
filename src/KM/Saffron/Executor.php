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
            $name = $parameter->getName();
            $arguments[] = isset($parameters[$name]) ? $parameters[$name] : null;
        }

        $method->invokeArgs($controller, $arguments);

        return $controller;
    }
}
