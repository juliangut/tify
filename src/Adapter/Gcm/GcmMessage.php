<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Gcm;

use Zend\Json\Json;
use ZendService\Google\Exception\InvalidArgumentException;
use ZendService\Google\Exception\RuntimeException;
use ZendService\Google\Gcm\Message;

/**
 * Custom GCM service message.
 *
 * Implements notification payload parameters.
 */
class GcmMessage extends Message
{
    const DEFAULT_TTL = 2419200; // 4 weeks

    /**
     * @var array
     */
    protected $notificationPayload = [];

    /**
     * Get notification payload data.
     *
     * @return array
     */
    public function getNotificationPayload()
    {
        return $this->notificationPayload;
    }

    /**
     * Set notification payload data.
     *
     * @param array $payload
     *
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     *
     * @return $this
     */
    public function setNotificationPayload(array $payload)
    {
        $this->clearNotificationPayload();

        foreach ($payload as $key => $value) {
            $this->addNotificationPayload($key, $value);
        }

        return $this;
    }

    /**
     * Add notification payload data.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     *
     * @return $this
     */
    public function addNotificationPayload($key, $value)
    {
        $key = trim($key);

        if ($key === '') {
            throw new InvalidArgumentException('Notification payload key must be a non-empty string');
        }

        if (array_key_exists($key, $this->notificationPayload)) {
            throw new RuntimeException(sprintf('"%s" conflicts with current set notification payload data', $key));
        }

        $this->notificationPayload[$key] = $value;

        return $this;
    }

    /**
     * Clear notification payload data.
     */
    public function clearNotificationPayload()
    {
        $this->notificationPayload = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson()
    {
        $json = [];

        if (count($this->registrationIds)) {
            $json['registration_ids'] = $this->registrationIds;
        }
        if ($this->collapseKey) {
            $json['collapse_key'] = $this->collapseKey;
        }
        if ($this->delayWhileIdle) {
            $json['delay_while_idle'] = $this->delayWhileIdle;
        }
        if ($this->timeToLive !== self::DEFAULT_TTL) {
            $json['time_to_live'] = $this->timeToLive;
        }
        if ($this->restrictedPackageName) {
            $json['restricted_package_name'] = $this->restrictedPackageName;
        }
        if ($this->dryRun) {
            $json['dry_run'] = $this->dryRun;
        }

        $json = array_merge($json, $this->getCompoundPayload());

        return Json::encode($json);
    }

    /**
     * Retrieve payload.
     *
     * @return array
     */
    protected function getCompoundPayload()
    {
        $payload = [];

        if (count($this->data)) {
            $payload['data'] = $this->data;
        }
        if (count($this->notificationPayload)) {
            $payload['notification'] = $this->notificationPayload;
        }

        return $payload;
    }
}
