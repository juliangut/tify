<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Traits;

/**
 * Sandbox aware trait.
 */
trait SandboxTrait
{
    /**
     * Sandbox environment.
     *
     * @var bool
     */
    protected $sandbox;

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
     * {@inheritdoc}
     */
    public function isSandbox()
    {
        return $this->sandbox;
    }
}
