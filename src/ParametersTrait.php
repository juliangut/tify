<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat;

trait ParametersTrait
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Has parameter.
     *
     * @param string $parameter Key
     *
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return array_key_exists($parameter, $this->parameters);
    }

    /**
     * Get parameter.
     *
     * @param string $parameter Key
     * @param mixed  $default   Default
     *
     * @return mixed
     */
    public function getParameter($parameter, $default = null)
    {
        return $this->hasParameter($parameter) ? $this->parameters[$parameter] : $default;
    }

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
     * @param array $parameters Parameters
     *
     * @return \Jgut\Pushat\Model\BaseParameteredModel
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Set parameter.
     *
     * @param string $parameter Key
     * @param mixed  $value     Value
     *
     * @return mixed
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;

        return $value;
    }
}
