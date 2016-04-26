<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Message;

/**
 * Class GcmMessage
 */
class GcmMessage extends AbstractMessage
{
    /**
     * List of Google GCM service reserved parameters.
     *
     * @var array
     */
    protected static $reservedParameters = [
        'from',
        'collapse_key',
        'delay_while_idle',
        'time_to_live',
        'restricted_package_name',
        'dry_run',
    ];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setParameter($parameter, $value)
    {
        $parameter = trim($parameter);

        if (preg_match('/^(google|gcm)/', $parameter) || in_array($parameter, self::$reservedParameters)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" can not be used as a custom parameter as it is reserved', $parameter)
            );
        }

        $this->parameters[$parameter] = $value;

        return $this;
    }
}
