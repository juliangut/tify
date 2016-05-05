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
     * List of reserved payload data.
     *
     * @see https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG
     *          /Chapters/TheNotificationPayload.html
     * @see https://developers.google.com/cloud-messaging/http-server-ref#downstream-http-messages-json
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
        '/^priority$/',
        '/^content_available$/',
    ];

    /**
     * Default message options.
     *
     * @see self::$reserverRegex
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
     * Data payload.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $payload;

    /**
     * Constructor.
     *
     * @param array $parameters
     * @param array $payload
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
     * @return $this
     */
    public function setPayloadData(array $data)
    {
        $this->payload->clear();

        foreach ($data as $payload => $value) {
            $this->setPayload($payload, $value);
        }

        return $this;
    }

    /**
     * Has payload data.
     *
     * @param string $payload
     *
     * @return bool
     */
    public function hasPayload($payload)
    {
        return $this->payload->containsKey($payload);
    }

    /**
     * Get payload data.
     *
     * @param string $payload
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getPayload($payload, $default = null)
    {
        return $this->payload->containsKey($payload) ? $this->payload->get($payload) : $default;
    }

    /**
     * Set payload data.
     *
     * @param string $payload
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setPayload($payload, $value)
    {
        $payload = trim($payload);

        foreach (self::$reservedRegex as $reservedRegex) {
            if (preg_match($reservedRegex, $payload)) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" can not be used as custom data, starts or contains the reserved string "%s"',
                    $payload,
                    preg_replace('![/^$]!', '', $reservedRegex)
                ));
            }
        }

        $this->payload->set($payload, $value);

        return $this;
    }
}
