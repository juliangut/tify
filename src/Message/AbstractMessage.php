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
        $this->setOption('title', $title);

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
