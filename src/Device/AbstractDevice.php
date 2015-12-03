<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Device;

use Jgut\Pushat\ParametersTrait;

abstract class AbstractDevice
{
    use ParametersTrait;

    /**
     * @var string
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param string $token
     * @param array  $parameters
     */
    public function __construct($token, array $parameters = [])
    {
        $this->setToken($token);
        $this->parameters = $parameters;
    }

    /**
     * Get token.
     *
     * @return string
     */
    final public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token.
     *
     * @param string $token
     */
    abstract public function setToken($token);
}
