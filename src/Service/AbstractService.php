<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\ParametersTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractService
{
    use ParametersTrait;

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
     */
    public function __construct(array $parameters = [], $sandbox = false)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->getDefinedParameters());
        $resolver->setDefaults($this->getDefaultParameters());
        $resolver->setRequired($this->getRequiredParameters());

        $this->parameters = $resolver->resolve($parameters);
        $this->sandbox = (bool) $sandbox;
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
     */
    public function setSandbox($sandbox)
    {
        $this->sandbox = (bool) $sandbox;

        return $this;
    }
}
