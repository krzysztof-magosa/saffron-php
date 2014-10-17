<?php
namespace KM\Saffron;

class RoutingResult
{
    protected $successful;
    protected $methodNotAllowed;
    protected $resourceNotFound;
    protected $allowedMethods;
    protected $target;
    protected $parameters;

    public function setSuccessful($value)
    {
        $this->successful = $value;
        return $this;
    }

    public function setMethodNotAllowed($value)
    {
        $this->methodNotAllowed = $value;
        return $this;
    }

    public function setResourceNotFound($value)
    {
        $this->resourceNotFound = $value;
        return $this;
    }

    public function setAllowedMethods(array $values)
    {
        $this->allowedMethods = $values;
        return $this;
    }

    public function setTarget(array $value)
    {
        $this->target = $value;
        return $this;
    }

    public function setParameters(array $values)
    {
        $this->parameters = $values;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * @return bool
     */
    public function isMethodNotAllowed()
    {
        return $this->methodNotAllowed;
    }

    /**
     * @return bool
     */
    public function isResourceNotFound()
    {
        return $this->resourceNotFound;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    /**
     * @return array
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
