<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Notification;

use InvalidArgumentException;
use Jgut\Pushat\Adapter\AbstractAdapter;
use Jgut\Pushat\Adapter\Apns as ApnsAdapter;
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
     * @param \Jgut\Pushat\Adapter\Apns  $adapter
     * @param \Jgut\Pushat\Message\Apns  $message
     * @param \Jgut\Pushat\Device\Apns[] $devices
     * @param array                      $options
     */
    public function __construct(ApnsAdapter $adapter, ApnsMessage $message, array $devices = [], array $options = [])
    {
        parent::__construct($adapter, $message, $devices, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setAdapter(AbstractAdapter $adapter)
    {
        if (!$adapter instanceof ApnsAdapter) {
            throw new InvalidArgumentException('Adapter must be an accepted APNS adapter');
        }

        $this->adapter = $adapter;

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
            throw new InvalidArgumentException('Message must be an accepted APNS message');
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
            throw new InvalidArgumentException('Device must be an accepted APNS device');
        }

        $this->devices[] = $device;

        return $this;
    }
}
