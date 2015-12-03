<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Message;

class Gcm extends AbstractMessage
{
    /**
     * {@inheritdoc}
     */
    protected $defaultOptions = [
        'title' => null,
        'body' => null,
        //'icon' => null,
        //'sound' => 'default',
        //'tag' => null,
        //'color' => null,
        //'click_action' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'body_loc_key' => null,
        //'body_loc_args' => null,
    ];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setParameter($parameter, $value)
    {
        static $reserved = [
            'from',
            'collapse_key',
            'delay_while_idle',
            'time_to_live',
            'restricted_package_name',
            'dry_run'
        ];
        $parameter = trim($parameter);

        if (preg_match('/^(google|gcm)/', $parameter) || in_array($parameter, $reserved)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" can not be used as a custom parameter as it is reserved', $parameter)
            );
        }

        $this->parameters[$parameter] = $value;

        return $value;
    }
}
