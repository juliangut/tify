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

/**
 * Parameter handling
 */
trait ParameterTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
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
        $this->initializeParameters();

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
            $this->parameters->set(trim($parameter), $value);
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

        $this->parameters->set(trim($parameter), $value);

        return $this;
    }
}