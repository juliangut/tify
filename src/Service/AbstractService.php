<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use Jgut\Pushat\ParametersTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractService
{
    const ENVIRONMENT_DEV  = 'dev';
    const ENVIRONMENT_PROD = 'prod';

    use ParametersTrait;

    /**
     * List of defined parameters.
     *
     * @var array
     */
    protected $definedParameters = [];

    /**
     * List of default parameters.
     *
     * @var array
     */
    protected $defaultParameters = [];

    /**
     * List of required parameters.
     *
     * @var array
     */
    protected $requiredParameters = [];

    /**
     * @var string
     */
    protected $serviceKey;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Constructor.
     *
     * @param array  $parameters
     * @param string $environment
     */
    public function __construct(array $parameters = [], $environment = self::ENVIRONMENT_PROD)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->definedParameters);
        $resolver->setDefaults($this->defaultParameters);
        $resolver->setRequired($this->requiredParameters);

        $reflectedClass   = new \ReflectionClass($this);
        $this->serviceKey = lcfirst($reflectedClass->getShortName());
        $this->parameters = $resolver->resolve($parameters);

        $this->setEnvironment($environment);
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return ucfirst($this->getServiceKey());
    }

    /**
     * Get ServiceKey.
     *
     * @return string
     */
    public function getServiceKey()
    {
        return $this->serviceKey;
    }

    /**
     * Get Environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set Environment.
     *
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $environment = strtolower(trim($environment));
        if (!in_array($environment, [static::ENVIRONMENT_DEV, static::ENVIRONMENT_PROD])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid environment', $environment));
        }

        $this->environment = $environment;

        return $this;
    }

    /**
     * isDevelopmentEnvironment.
     *
     * @return bool
     */
    public function isDevelopmentEnvironment()
    {
        return ($this->getEnvironment() === static::ENVIRONMENT_DEV);
    }

    /**
     * isProductionEnvironment.
     *
     * @return bool
     */
    public function isProductionEnvironment()
    {
        return ($this->getEnvironment() === static::ENVIRONMENT_PROD);
    }
}
