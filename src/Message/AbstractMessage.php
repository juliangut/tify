<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Message;

use Jgut\Tify\OptionsTrait;
use Jgut\Tify\ParametersTrait;

abstract class AbstractMessage
{
    use OptionsTrait;
    use ParametersTrait;

    /**
     * Default message options.
     *
     * @var array
     */
    protected $defaultOptions = [
        'title' => null,
        'body' => null,

        // GCM
        //'icon' => null,
        //'sound' => 'default',
        //'badge' => 'null',
        //'tag' => null,
        //'color' => null,
        //'click_action' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'body_loc_key' => null,
        //'body_loc_args' => null,

        // APNS
        //'loc_key' => null,
        //'loc_args' => null,
        //'launch_image' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'action_loc_key' => null,
    ];

    /**
     * Constructor.
     *
     * @param array $options
     * @param array $parameters
     */
    public function __construct(array $options = [], array $parameters = [])
    {
        $this->setOptions(array_merge($this->defaultOptions, $options));
        $this->setParameters($parameters);
    }

    /**
     * Convenience method to set message title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->setOption('title', trim($title));

        return $this;
    }

    /**
     * Convenience method to set message body.
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->setOption('body', $body);

        return $this;
    }
}
