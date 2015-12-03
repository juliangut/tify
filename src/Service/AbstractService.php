<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use InvalidArgumentException;
use ReflectionClass;
use Jgut\Pushat\ParametersTrait;
use Jgut\Pushat\Notification\AbstractNotification;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractService
{
    const ENVIRONMENT_DEV  = 'dev';
    const ENVIRONMENT_PROD = 'prod';

    use ParametersTrait;

    /**
     * @var string
     */
    protected $serviceKey;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param string $environment
     * @param array  $parameters
     */
    public function __construct($environment = self::ENVIRONMENT_PROD, array $parameters = [])
    {
        $this->setEnvironment($environment);

        $resolver = new OptionsResolver();
        $resolver->setDefined($this->getDefinedParameters());
        $resolver->setDefaults($this->getDefaultParameters());
        $resolver->setRequired($this->getRequiredParameters());

        $reflectedClass   = new ReflectionClass($this);
        $this->serviceKey = lcfirst($reflectedClass->getShortName());
        $this->parameters = $resolver->resolve($parameters);
    }

    /**
     * Send a notification.
     *
     * @param \Jgut\Pushat\Notification\AbstractNotification $notification
     *
     * @return array
     */
    abstract public function send(AbstractNotification $notification);

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
            throw new InvalidArgumentException(sprintf('"%s" is not a valid environment', $environment));
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

    /**
     * Return the original response.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get defined parameters.
     *
     * @return array
     */
    abstract protected function getDefinedParameters();

    /**
     * Get default parameters.
     *
     * @return array
     */
    abstract protected function getDefaultParameters();

    /**
     * Get required parameters.
     *
     * @return array
     */
    abstract protected function getRequiredParameters();
}
