<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Message;

class Apns extends AbstractMessage
{
    /**
     * {@inheritdoc}
     */
    protected $defaultOptions = [
        'title' => null,
        'body' => null,
        //'loc_key' => null,
        //'loc_args' => null,
        //'launch_image' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'action_loc_key' => null,
    ];
}
