<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

/**
 * Push message.
 */
class Message
{
    use ParameterTrait;
    use PayloadTrait;

    /**
     * List of reserved parameters.
     *
     * @var array
     */
    protected static $reservedRegex = [
        // APNS
        '/^apc$/',

        // GCM
        '/^(google|gcm)/',
        '/^from$/',
        '/^collapse_key$/',
        '/^delay_while_idle$/',
        '/^time_to_live$/',
        '/^restricted_package_name$/',
        '/^dry_run$/',
    ];

    /**
     * Default message options.
     *
     * @var array
     */
    protected $defaultParameters = [
        'title' => null,
        'body' => null,

        // APNS
        //'loc_key' => null,
        //'loc_args' => null,
        //'launch_image' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'action_loc_key' => null,

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
    ];

    /**
     * Constructor.
     *
     * @param array $parameters
     * @param array $payload
     */
    public function __construct(array $parameters = [], array $payload = [])
    {
        $this->setParameters(array_merge($this->defaultParameters, $parameters));
        $this->setPayload($payload);
    }

    /**
     * Convenience method to set message title.
     *
     * @param string $title
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setParameter('title', $title);

        return $this;
    }

    /**
     * Convenience method to set message body.
     *
     * @param string $body
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->setParameter('body', $body);

        return $this;
    }

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

        foreach (self::$reservedRegex as $reservedRegex) {
            if (preg_match($reservedRegex, $parameter)) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" can not be used as a custom parameter, contains a reserved string',
                    $parameter
                ));
            }
        }

        $this->parameters[$parameter] = $value;

        return $this;
    }
}
