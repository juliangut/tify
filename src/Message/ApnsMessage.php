<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Message;

class ApnsMessage extends AbstractMessage
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setParameter($parameter, $value)
    {
        $parameter = trim($parameter);

        if ($parameter === 'apc') {
            throw new \InvalidArgumentException('"apc" can not be used as a custom parameter as it is reserved');
        }

        $this->parameters[$parameter] = $value;

        return $value;
    }
}
