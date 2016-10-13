<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

/**
 * Push message.
 */
class Message
{
    use ParameterTrait {
        ParameterTrait::setParameter as innerSetParameter;
    }

    const PARAMETER_TITLE = 'title';
    const PARAMETER_BODY = 'body';
    const PARAMETER_TITLE_LOC_KEY = 'title_loc_key';
    const PARAMETER_TITLE_LOC_ARGS = 'title_loc_args';
    const PARAMETER_BODY_LOC_KEY = 'body_loc_key';
    const PARAMETER_BODY_LOC_ARGS = 'body_loc_args';

    // APNS specific
    const PARAMETER_ACTION_LOC_KEY = 'action-loc-key';
    const PARAMETER_LAUNCH_IMAGE = 'launch-image';

    // GCM specific
    const PARAMETER_ICON = 'icon';
    const PARAMETER_SOUND = 'sound';
    const PARAMETER_TAG = 'tag';
    const PARAMETER_COLOR = 'color';
    const PARAMETER_CLICK_ACTION = 'click_action';

    /**
     * Reserved payload keys.
     *
     * For an iOS silent notification leave message with no parameters at all, not even title nor body.
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
     * @var array
     */
    protected $payload = [];

    /**
     * Constructor.
     *
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [])
    {
        $this->parameterAliasMap = [
            'title-loc-key' => static::PARAMETER_TITLE_LOC_KEY,
            'title-loc-args' => static::PARAMETER_TITLE_LOC_ARGS,
            'loc-key' => static::PARAMETER_BODY_LOC_KEY,
            'loc-args' => static::PARAMETER_BODY_LOC_ARGS,
        ];

        $this->setParameters($parameters);
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
        $this->setParameter(static::PARAMETER_TITLE, $title);

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
        $this->setParameter(static::PARAMETER_BODY, $body);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setParameter($parameter, $value)
    {
        $parameter = $this->getMappedParameter($parameter);

        $self = new \ReflectionClass(static::class);
        if (!in_array($parameter, $self->getConstants())) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid message parameter', $parameter));
        }

        return $this->innerSetParameter($parameter, $value);
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
        return $this->payload;
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
        $this->payload = [];

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
        return array_key_exists($this->composePayloadKey($key), $this->payload);
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

        return array_key_exists($key, $this->payload) ? $this->payload[$key] : $default;
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

        $this->payload[$key] = $value;

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
