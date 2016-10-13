<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

/**
 * Parameter handling.
 */
trait ParameterTrait
{
    /**
     * Parameter alias key map.
     *
     * @var array
     */
    protected $parameterAliasMap = [];

    /**
     * List of parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = [];

        foreach ($parameters as $parameter => $value) {
            $this->setParameter($parameter, $value);
        }

        return $this;
    }

    /**
     * Has parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return array_key_exists($this->getMappedParameter($parameter), $this->parameters);
    }

    /**
     * Easy access to parameters.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments = null)
    {
        if (preg_match('/^get([A-Z].+)$/', $name, $matches)) {
            return $this->getParameter(lcfirst($matches[1]));
        }

        return;
    }

    /**
     * Get parameter.
     *
     * @param string $parameter
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($parameter, $default = null)
    {
        $parameter = $this->getMappedParameter($parameter);

        return array_key_exists($parameter, $this->parameters) ? $this->parameters[$parameter] : $default;
    }

    /**
     * Set parameter.
     *
     * @param string $parameter
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$this->getMappedParameter($parameter)] = $value;

        return $this;
    }

    /**
     * Get normalized service parameter.
     *
     * @param string $parameter
     *
     * @return string
     */
    protected function getMappedParameter($parameter)
    {
        $parameter = trim($parameter);

        if (array_key_exists($parameter, $this->parameterAliasMap)) {
            return $this->parameterAliasMap[$parameter];
        }

        return $parameter;
    }
}
