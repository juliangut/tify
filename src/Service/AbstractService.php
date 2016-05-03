<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Exception\ServiceException;
use Jgut\Tify\ParameterTrait;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractService.
 */
abstract class AbstractService
{
    use ParameterTrait;

    /**
     * Sandbox environment.
     *
     * @var bool
     */
    protected $sandbox;

    /**
     * Constructor.
     *
     * @param array $parameters
     * @param bool  $sandbox
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     */
    public function __construct(array $parameters = [], $sandbox = false)
    {
        $parametersResolver = new OptionsResolver();

        $parametersResolver->setDefined($this->getDefinedParameters());
        $parametersResolver->setDefaults($this->getDefaultParameters());
        $parametersResolver->setRequired($this->getRequiredParameters());

        try {
            $this->parameters = $parametersResolver->resolve($parameters);
        } catch (MissingOptionsException $exception) {
            throw new ServiceException(sprintf('Missing parameters on "%s"', self::class));
        } catch (\Exception $exception) {
            throw new ServiceException('Invalid parameter provided');
        }

        $this->sandbox = (bool) $sandbox;
    }

    /**
     * Retrieve if sandbox.
     *
     * @return bool
     */
    public function isSandbox()
    {
        return (bool) $this->sandbox;
    }

    /**
     * Set Sandbox.
     *
     * @param bool $sandbox
     *
     * @return $this
     */
    public function setSandbox($sandbox)
    {
        $this->sandbox = (bool) $sandbox;

        return $this;
    }

    /**
     * Get the list of defined parameters.
     *
     * @return array
     */
    protected function getDefinedParameters()
    {
        return [];
    }

    /**
     * Get the list of default parameters.
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        return [];
    }

    /**
     * Get the list of required parameters.
     *
     * @return array
     */
    protected function getRequiredParameters()
    {
        return [];
    }
}
