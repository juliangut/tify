<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Device;

class Gcm extends AbstractDevice
{
    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $token = trim($token);
        if ($token === '') {
            throw new \InvalidArgumentException('GCM token can not be empty');
        }

        $this->token = $token;

        return $this;
    }
}
