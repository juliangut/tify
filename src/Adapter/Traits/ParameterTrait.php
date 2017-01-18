<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Traits;

use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\ParameterTrait as ParamsTrait;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Parameter aware trait.
 */
trait ParameterTrait
{
    use ParamsTrait;

    /**
     * Assign adapter parameters.
     *
     * @param array $parameters
     *
     * @throws AdapterException
     */
    protected function assignParameters(array $parameters = [])
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
    }

    /**
     * Get the list of defined parameters.
     *
     * @return array
     */
    abstract protected function getDefinedParameters();

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
    abstract protected function getRequiredParameters();
}
