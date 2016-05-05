<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Push message.
 */
class Message
{
    use ParameterTrait;

    /**
     * Default message parameters.
     *
     * @var array
     */
    protected $defaultParameters = [
        'title' => null,
        'body' => null,

        // APNS
        //'launch_image' => null,
        //'action_loc_key' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'loc_key' => null,
        //'loc_args' => null,

        // GCM
        //'icon' => null,
        //'sound' => 'default',
        //'tag' => null,
        //'color' => '#rrggbb',
        //'click_action' => null,
        //'title_loc_key' => null,
        //'title_loc_args' => null,
        //'body_loc_key' => null,
        //'body_loc_args' => null,
    ];

    /**
     * Reserved payload keys.
     *
     * @var array
     */
    protected static $reservedKeyRegex = [
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
        '/^priority$/',
        '/^content_available$/',
    ];

    /**
     * Payload prefix
     *
     * @var string
     */
    protected $payloadPrefix = 'data_';

    /**
     * Message payload.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $payload;

    /**
     * Constructor.
     *
     * @param array $parameters
     * @param array $payload
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [], array $payload = [])
    {
        $this->setParameters(array_merge($this->defaultParameters, $parameters));

        $this->payload = new ArrayCollection;

        $this->setPayloadData($payload);
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
     * Retrieve payload prefix.
     *
     * @return string
     */
    public function getPayloadPrefix()
    {
        return $this->payloadPrefix;
    }

    /**
     * Set payload prefix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setPayloadPrefix($prefix)
    {
        $this->payloadPrefix = trim($prefix);

        return $this;
    }

    /**
     * Get payload data.
     *
     * @return array
     */
    public function getPayloadData()
    {
        return $this->payload->toArray();
    }

    /**
     * Set payload data.
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setPayloadData(array $data)
    {
        $this->payload->clear();

        foreach ($data as $key => $value) {
            $this->setPayload($key, $value);
        }

        return $this;
    }

    /**
     * Has payload data.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function hasPayload($key)
    {
        return $this->payload->containsKey($this->composePayloadKey($key));
    }

    /**
     * Get payload data.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function getPayload($key, $default = null)
    {
        $key = $this->composePayloadKey($key);

        return $this->payload->containsKey($key) ? $this->payload->get($key) : $default;
    }

    /**
     * Set payload data.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setPayload($key, $value)
    {
        $key = $this->composePayloadKey($key);

        foreach (self::$reservedKeyRegex as $reservedKeyRegex) {
            if (preg_match($reservedKeyRegex, $key)) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" can not be used as message payload key, starts or contains the reserved string "%s"',
                    $key,
                    preg_replace('![/^$]!', '', $reservedKeyRegex)
                ));
            }
        }

        $this->payload->set($key, $value);

        return $this;
    }

    /**
     * Compose payload key with prefix.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function composePayloadKey($key)
    {
        $key = trim($key);

        if ($key === '') {
            throw new \InvalidArgumentException('Payload parameter key can not be empty');
        }

        return $this->payloadPrefix . $key;
    }
}
