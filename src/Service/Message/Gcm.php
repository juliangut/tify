<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

use ZendService\Google\Exception\InvalidArgumentException;
use ZendService\Google\Exception\RuntimeException;
use ZendService\Google\Gcm\Message as ServiceMessage;
use Zend\Json\Json;

/**
 * Custom GCM service message.
 *
 * Implements notification payload parameters.
 */
class Gcm extends ServiceMessage
{
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
     * @param array $data
     */
    public function setNotificationPayload(array $payload)
    {
        $this->clearNotificationPayload();

        foreach ($payload as $k => $v) {
            $this->addNotificationPayload($k, $v);
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
     */
    public function addNotificationPayload($key, $value)
    {
        $key = trim($key);

        if ($key === '') {
            throw new InvalidArgumentException('$key must be a non-empty string');
        }

        if (array_key_exists($key, $this->notificationPayload)) {
            throw new RuntimeException('$key conflicts with current set notification payload data');
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

        if ($this->registrationIds) {
            $json['registration_ids'] = $this->registrationIds;
        }
        if ($this->collapseKey) {
            $json['collapse_key'] = $this->collapseKey;
        }
        if ($this->delayWhileIdle) {
            $json['delay_while_idle'] = $this->delayWhileIdle;
        }
        if ($this->timeToLive != 2419200) {
            $json['time_to_live'] = $this->timeToLive;
        }

        $json = array_merge($json, $this->getPayload());

        return Json::encode($json);
    }

    /**
     * Retrieve payload.
     *
     * @return array
     */
    protected function getPayload()
    {
        $payload = [];

        if ($this->data) {
            $payload['data'] = $this->data;
        }
        if ($this->notificationPayload) {
            $payload['notification'] = $this->notificationPayload;
        }

        return $payload;
    }
}
