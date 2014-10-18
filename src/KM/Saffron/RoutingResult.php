<?php
namespace KM\Saffron;

class RoutingResult
{
    protected $successful = false;
    protected $methodNotAllowed = false;
    protected $resourceNotFound = false;
    protected $allowedMethods = [];
    protected $target = [];
    protected $parameters = [];

    public function __construct($successful, $methodNotAllowed, $resourceNotFound, array $allowedMethods, array $target, array $parameters)
    {
        $this->successful = $successful;
        $this->methodNotAllowed = $methodNotAllowed;
        $this->resourceNotFound = $resourceNotFound;
        $this->allowedMethods = $allowedMethods;
        $this->target = $target;
        $this->parameters = $parameters;
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
