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
}
