<?php
namespace KM\Saffron;

class Executor
{
    protected $controller;
    protected $method;
    protected $parameters = [];

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

    public function fire()
    {
        $method = new \ReflectionMethod($this->controller, $this->method);
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $arguments[] = isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        return $method->invokeArgs($this->controller, $arguments);
    }
}
