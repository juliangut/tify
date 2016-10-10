<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Push message.
 */
class Message
{
    use ParameterTrait {
        ParameterTrait::hasParameter as hasDefinedParameter;
        ParameterTrait::getParameter as getDefinedParameter;
        ParameterTrait::setParameter as setDefinedParameter;
    }

    /**
     * Valid message parameters.
     *
     * @see https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG
     *          /Chapters/TheNotificationPayload.html
     * @see https://developers.google.com/cloud-messaging/http-server-ref#downstream-http-messages-json
     *
     * @var array
     */
    protected static $validParameters = [
        // Common
        'title',
        'body',

        // Common mapped
        'title_loc_key',
        'title_loc_args',
        'body_loc_key',
        'body_loc_args',

        // APNS specific
        'action-loc-key',
        'launch-image',

        // GCM specific
        'icon',
        'sound',
        'tag',
        'color',
        'click_action',
    ];

    /**
     * Parameter key map.
     *
     * @var array
     */
    protected static $parameterMap = [
        'title-loc-key'  => 'title_loc_key',
        'title-loc-args' => 'title_loc_args',
        'loc-key'        => 'body_loc_key',
        'loc-args'       => 'body_loc_args',
    ];

    /**
     * Reserved payload keys.
     *
     * @var array
     */
    protected static $reservedPayloadRegex = [
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
     * Payload prefix.
     *
     * @var string
     */
    protected $payloadPrefix = 'data_';

    /**
     * Message payload.
     *
     * @var ArrayCollection
     */
    protected $payload;

    /**
     * Constructor.
     *
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [])
    {
        $this->setParameters($parameters);

        $this->payload = new ArrayCollection;
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
     */
    public function hasParameter($parameter)
    {
        return $this->hasDefinedParameter($this->getMappedParameter($parameter));
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($parameter, $default = null)
    {
        return $this->getDefinedParameter($this->getMappedParameter($parameter), $default);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setParameter($parameter, $value)
    {
        $parameter = $this->getMappedParameter($parameter);

        if (!in_array($parameter, static::$validParameters)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid message parameter', $parameter));
        }

        return $this->setDefinedParameter($parameter, $value);
    }

    /**
     * Get normalized service parameter.
     *
     * @param string $parameter
     *
     * @return string
     */
    private function getMappedParameter($parameter)
    {
        if (array_key_exists($parameter, static::$parameterMap)) {
            return static::$parameterMap[$parameter];
        }

        return $parameter;
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

        foreach (static::$reservedPayloadRegex as $keyRegex) {
            if (preg_match($keyRegex, $key)) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" can not be used as message payload key, starts with or contains "%s"',
                    $key,
                    preg_replace('![/^$]!', '', $keyRegex)
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
        $key = $this->payloadPrefix . trim($key);

        if ($key === '') {
            throw new \InvalidArgumentException('Message payload parameter can not be empty');
        }

        return $key;
    }
}
