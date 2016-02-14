<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Recipient;

use Jgut\Tify\ParametersTrait;

abstract class AbstractRecipient
{
    use ParametersTrait;

    /**
     * Recipient token.
     *
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
