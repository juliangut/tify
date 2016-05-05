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
 * Class GcmReceiver
 */
class GcmReceiver extends AbstractReceiver
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
        if (trim($token) === '') {
            throw new \InvalidArgumentException('GCM token can not be empty');
        }

        $this->token = trim($token);

        return $this;
    }
}
