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
use Jgut\Pushat\Service\AbstractService;
use Jgut\Pushat\Service\Gcm as GcmService;
use Jgut\Pushat\Device\AbstractDevice;
use Jgut\Pushat\Device\Gcm as GcmDevice;
use Jgut\Pushat\Message\AbstractMessage;
use Jgut\Pushat\Message\Gcm as GcmMessage;

class Gcm extends AbstractNotification
{
    /**
     * {@inheritdoc}
     */
    protected $defaultOptions = [
        'collapse_key' => null,
        'delay_while_idle' => false,
        'time_to_live' => 600,
        'restricted_package_name' => null,
        'dry_run' => false,
    ];

    /**
     * @param \Jgut\Pushat\Service\Gcm  $service
     * @param \Jgut\Pushat\Message\Gcm  $message
     * @param \Jgut\Pushat\Device\Gcm[] $devices
     * @param array                     $options
     */
    public function __construct(GcmService $service, GcmMessage $message, array $devices = [], array $options = [])
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
        if (!$service instanceof GcmService) {
            throw new InvalidArgumentException('Service must be an accepted GCM service');
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
        if (!$message instanceof GcmMessage) {
            throw new InvalidArgumentException('Message must be an accepted GCM message');
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
        if (!$device instanceof GcmDevice) {
            throw new InvalidArgumentException('Device must be an accepted GCM device');
        }

        $this->devices[] = $device;

        return $this;
    }
}
