<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Recipient;

class Apns extends AbstractRecipient
{
    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $token = trim($token);
        if (!ctype_xdigit($token) || strlen($token) !== 64) {
            throw new \InvalidArgumentException('APNS token must be a 64 hex string');
        }

        $this->token = $token;

        return $this;
    }
}
