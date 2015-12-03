<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Device;

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
