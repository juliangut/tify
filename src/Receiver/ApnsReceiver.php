<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Receiver;

/**
 * Class ApnsReceiver
 */
class ApnsReceiver extends AbstractReceiver
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setToken($token)
    {
        if (!ctype_xdigit($token) || strlen(trim($token)) !== 64) {
            throw new \InvalidArgumentException('APNS token must be a 64 hex string');
        }

        $this->token = trim($token);

        return $this;
    }
}
