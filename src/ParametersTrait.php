<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;

trait ParametersTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $parameters = [];

    /**
     * Initialize options collection.
     */
    protected function initializeParameters()
    {
        if (!$this->parameters instanceof ArrayCollection) {
            $this->parameters = new ArrayCollection;
        }
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
        $this->initializeParameters();

        return $this->parameters->containsKey($parameter);
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
        $this->initializeParameters();

        return $this->parameters->containsKey($parameter) ? $this->parameters->get($parameter) : $default;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        $this->initializeParameters();

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
        if (!$this->parameters instanceof ArrayCollection) {
            $this->parameters->clear();
        } else {
            $this->initializeParameters();
        }

        foreach ($parameters as $parameter => $value) {
            $this->parameters->set($parameter, $value);
        }

        return $this;
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

        $this->parameters->set($parameter, $value);

        return $this;
    }
}
