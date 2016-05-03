<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Recipient;

/**
 * Class AbstractRecipient
 */
abstract class AbstractRecipient
{
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
     */
    public function __construct($token)
    {
        $this->setToken($token);
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
