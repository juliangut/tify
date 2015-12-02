<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Message;

use Jgut\Pushat\OptionsTrait;
use Jgut\Pushat\ParametersTrait;

abstract class AbstractMessage
{
    use OptionsTrait;
    use ParametersTrait;

    /**
     * Default message options.
     *
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * Constructor.
     *
     * @param array $options    Options
     * @param array $parameters Parameters
     */
    public function __construct(array $options = [], array $parameters = [])
    {
        $this->options = array_merge($this->defaultOptions, $options);
        $this->parameters = $parameters;
    }

    /**
     * Shortcut to set message title.
     *
     * @param string $title Message title
     */
    public function setTitle($title)
    {
        $this->setOption('title', trim($title));

        return $this;
    }

    /**
     * Shortcut to set message body.
     *
     * @param string $body Message body
     */
    public function setBody($body)
    {
        $this->setOption('body', $body);

        return $this;
    }
}
