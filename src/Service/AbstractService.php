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
        $resolver->setDefined($this->definedParameters);
        $resolver->setDefaults($this->defaultParameters);
        $resolver->setRequired($this->requiredParameters);

        $this->parameters = $resolver->resolve($parameters);
        $this->setSandbox($sandbox);
    }

    /**
     * Retrieve if sandbox.
     *
     * @return bool
     */
    public function isSandbox()
    {
        return $this->sandbox;
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
