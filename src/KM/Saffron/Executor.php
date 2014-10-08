<?php
namespace KM\Saffron;

class Executor
{
    protected $controller;
    protected $method;
    protected $parameters = [];
    protected $preDispatch;
    protected $postDispatch;

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

    public function fire()
    {
        if ($this->preDispatch) {
            $this->preDispatch($this->controller, $this->method, $this->parameters);
        }

        $method = new \ReflectionMethod($this->controller, $this->method);
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $arguments[] = isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        $result = $method->invokeArgs($this->controller, $arguments);

        if ($this->postDispatch) {
            $this->postDispatch($this->controller, $this->method, $this->parameters);
        }

        return $result;
    }
}
