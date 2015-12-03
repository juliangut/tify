<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service\Message;

use Zend\Json\Json;
use ZendService\Google\Exception\InvalidArgumentException;
use ZendService\Google\Exception\RuntimeException;
use ZendService\Google\Gcm\Message as ServiceMessage;

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
        if ($this->restrictedPackageName) {
            $json['restricted_package_name'] = $this->restrictedPackageName;
        }
        if ($this->dryRun) {
            $json['dry_run'] = $this->dryRun;
        }
        if ($this->data) {
            $json['data'] = $this->data;
        }
        if ($this->notificationPayload) {
            $json['notification'] = $this->notificationPayload;
        }

        return Json::encode($json);
    }
}
