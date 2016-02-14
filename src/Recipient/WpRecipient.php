<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Recipient;

class WpRecipient extends AbstractRecipient
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setToken($token)
    {
        $token = trim($token);
        if (!filter_var($token, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Windows Phone tokens must be a valid URL endpoint');
        }

        $this->token = $token;

        return $this;
    }
}
