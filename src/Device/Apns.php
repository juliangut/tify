<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Device;

use InvalidArgumentException;

class Apns extends AbstractDevice
{
    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $token = trim($token);
        if (!ctype_xdigit($token) || strlen($token) !== 64) {
            throw new InvalidArgumentException('APNS token must be a 64 hex string');
        }

        $this->token = $token;
    }
}
