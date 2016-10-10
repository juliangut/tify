<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Stubs;

use Jgut\Tify\Adapter\AbstractAdapter;

/**
 * Service adapter stub.
 */
class AdapterStub extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['param1'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['param1'];
    }
}
