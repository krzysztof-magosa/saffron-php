<?php
namespace KM\Saffron;

class Executor
{
    /**
     * Build controller object, runs its method with provided parameters.
     * It uses reflection to unpack parameters to proper method arguments.
     * 
     * @param string $controllerName Name of controller
     * @param string $actionName Name of action (method)
     * @param array $parameters Parameters to be passed to action
     * @return mixed Controller object
     */
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
