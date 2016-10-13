<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter;

use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\ParameterTrait;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract service class.
 */
abstract class AbstractAdapter implements Adapter
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
     * @throws AdapterException
     */
    public function __construct(array $parameters = [], $sandbox = false)
    {
        $parametersResolver = new OptionsResolver;

        $parametersResolver->setDefined($this->getDefinedParameters());
        $parametersResolver->setDefaults($this->getDefaultParameters());
        $parametersResolver->setRequired($this->getRequiredParameters());

        try {
            $this->parameters = $parametersResolver->resolve($parameters);
        } catch (MissingOptionsException $exception) {
            throw new AdapterException(sprintf('Missing parameters on "%s"', static::class));
        } catch (\Exception $exception) {
            throw new AdapterException('Invalid parameter provided');
        }

        $this->sandbox = (bool) $sandbox;
    }

    /**
     * {@inheritdoc}
     */
    public function isSandbox()
    {
        return $this->sandbox;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setSandbox($sandbox = true)
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
