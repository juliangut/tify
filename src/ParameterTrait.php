<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var ArrayCollection
     */
    protected $parameters;

    /**
     * Initialize parameters collection.
     */
    protected function initializeParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = new ArrayCollection;
        }
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        if (!$this->parameters instanceof ArrayCollection) {
            return [];
        }

        return $this->parameters->toArray();
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
        if ($this->parameters instanceof ArrayCollection) {
            $this->parameters->clear();
        } else {
            $this->initializeParameters();
        }

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
        if (!$this->parameters instanceof ArrayCollection) {
            return false;
        }

        return $this->parameters->containsKey($this->getMappedParameter($parameter));
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
        if (!$this->parameters instanceof ArrayCollection) {
            return $default;
        }

        $parameter = $this->getMappedParameter($parameter);

        return $this->parameters->containsKey($parameter) ? $this->parameters->get($parameter) : $default;
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
        $this->initializeParameters();

        $this->parameters->set($this->getMappedParameter($parameter), $value);

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
