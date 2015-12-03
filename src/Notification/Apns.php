<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Notification;

use Jgut\Pushat\Service\AbstractService;
use Jgut\Pushat\Service\Apns as ApnsService;
use Jgut\Pushat\Device\AbstractDevice;
use Jgut\Pushat\Device\Apns as ApnsDevice;
use Jgut\Pushat\Message\AbstractMessage;
use Jgut\Pushat\Message\Apns as ApnsMessage;

class Apns extends AbstractNotification
{
    /**
     * {@inheritdoc}
     */
    protected $defaultOptions = [
        'expire' => null,
        'badge' => null,
        'sound' => 'bingbong.aiff',
        'content_available' => null,
        'category' => null,
    ];

    /**
     * @param \Jgut\Pushat\Service\Apns  $service
     * @param \Jgut\Pushat\Message\Apns  $message
     * @param \Jgut\Pushat\Device\Apns[] $devices
     * @param array                      $options
     */
    public function __construct(ApnsService $service, ApnsMessage $message, array $devices = [], array $options = [])
    {
        parent::__construct($service, $message, $devices, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setService(AbstractService $service)
    {
        if (!$service instanceof ApnsService) {
            throw new \InvalidArgumentException('Service must be an accepted APNS service');
        }

        $this->service = $service;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setMessage(AbstractMessage $message)
    {
        if (!$message instanceof ApnsMessage) {
            throw new \InvalidArgumentException('Message must be an accepted APNS message');
        }

        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function addDevice(AbstractDevice $device)
    {
        if (!$device instanceof ApnsDevice) {
            throw new \InvalidArgumentException('Device must be an accepted APNS device');
        }

        $this->devices[] = $device;

        return $this;
    }
}
